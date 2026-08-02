<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Product extends Model
{
    protected string $table = 'products';
    protected bool $softDelete = true;

    private const SELECT = 'p.*, c.name AS category_name, c.slug AS category_slug,
                            b.name AS brand_name, b.slug AS brand_slug';
    private const JOINS = 'FROM products p
                           LEFT JOIN categories c ON c.id = p.category_id
                           LEFT JOIN brands b ON b.id = p.brand_id';

    public function published(string $extra = '', array $params = [], string $order = 'p.published_at DESC', int $limit = 12): array
    {
        $where = 'p.status = "published" AND p.deleted_at IS NULL';
        if ($extra !== '') {
            $where .= " AND {$extra}";
        }
        return Database::all(
            self::assemble($where, $order) . " LIMIT {$limit}",
            $params
        );
    }

    private static function assemble(string $where, string $order): string
    {
        return 'SELECT ' . self::SELECT . ' ' . self::JOINS . " WHERE {$where} ORDER BY {$order}";
    }

    public function trending(int $limit = 8): array
    {
        return $this->published('p.is_trending = 1', [], 'p.views DESC, p.published_at DESC', $limit);
    }

    public function deals(int $limit = 8): array
    {
        return $this->published('p.is_deal = 1', [], 'p.discount DESC', $limit);
    }

    public function featured(int $limit = 8): array
    {
        return $this->published('p.is_featured = 1', [], 'p.published_at DESC', $limit);
    }

    public function popular(int $limit = 8): array
    {
        return $this->published('', [], 'p.views DESC', $limit);
    }

    public function latest(int $limit = 8): array
    {
        return $this->published('', [], 'p.published_at DESC', $limit);
    }

    /** Full public detail with primary image + gallery. */
    public function detailBySlug(string $slug): ?array
    {
        $row = Database::first(
            self::assemble('p.slug = ? AND p.status = "published" AND p.deleted_at IS NULL', 'p.id DESC'),
            [$slug]
        );
        if (!$row) {
            return null;
        }
        $row['images'] = Database::all(
            'SELECT path, alt, is_primary FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC',
            [$row['id']]
        );
        foreach (['features', 'specifications', 'pros', 'cons', 'faq'] as $json) {
            $row[$json] = $row[$json] ? json_decode((string) $row[$json], true) : [];
        }
        return $row;
    }

    public function primaryImage(int $productId): ?string
    {
        return Database::value(
            'SELECT path FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC LIMIT 1',
            [$productId]
        ) ?: null;
    }

    public function related(int $categoryId, int $excludeId, int $limit = 4): array
    {
        return $this->published(
            'p.category_id = ? AND p.id <> ?',
            [$categoryId, $excludeId],
            'p.views DESC',
            $limit
        );
    }

    public function incrementViews(int $id): void
    {
        Database::run('UPDATE products SET views = views + 1 WHERE id = ?', [$id]);
    }

    /** Faceted search used by public listing + AJAX filter. */
    public function search(array $filters, int $page, int $perPage): array
    {
        $where = ['p.status = "published"', 'p.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(p.name LIKE ? OR p.short_description LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }
        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = ?';
            $params[] = (int) $filters['brand_id'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = 'p.price >= ?';
            $params[] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = 'p.price <= ?';
            $params[] = (float) $filters['max_price'];
        }

        $order = match ($filters['sort'] ?? '') {
            'price_low'  => 'p.price ASC',
            'price_high' => 'p.price DESC',
            'rating'     => 'p.rating DESC',
            'popular'    => 'p.views DESC',
            default      => 'p.published_at DESC',
        };

        $whereSql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM products p WHERE {$whereSql}", $params);
        $offset = (max(1, $page) - 1) * $perPage;

        $rows = Database::all(
            self::assemble($whereSql, $order) . " LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'      => $rows,
            'total'     => $total,
            'page'      => max(1, $page),
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    /** Lightweight autocomplete. */
    public function suggest(string $term, int $limit = 8): array
    {
        return Database::all(
            'SELECT name, slug FROM products
             WHERE status = "published" AND deleted_at IS NULL AND name LIKE ?
             ORDER BY views DESC LIMIT ?',
            ['%' . $term . '%', $limit]
        );
    }

    public function recalculateRating(int $productId): void
    {
        $agg = Database::first(
            'SELECT AVG(rating) avg_r, COUNT(*) c FROM reviews WHERE product_id = ? AND is_approved = 1',
            [$productId]
        );
        Database::run(
            'UPDATE products SET rating = ?, rating_count = ? WHERE id = ?',
            [round((float) ($agg['avg_r'] ?? 0), 1), (int) ($agg['c'] ?? 0), $productId]
        );
    }
}
