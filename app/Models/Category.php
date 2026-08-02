<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Category extends Model
{
    protected string $table = 'categories';
    protected bool $softDelete = true;

    /** Top-level active categories for nav/menu. */
    public function menu(int $limit = 20): array
    {
        return Database::all(
            'SELECT id, name, slug, icon FROM categories
             WHERE is_active = 1 AND deleted_at IS NULL AND parent_id IS NULL
             ORDER BY sort_order ASC, name ASC LIMIT ?',
            [$limit]
        );
    }

    /** All active categories with product counts (for homepage grid). */
    public function withCounts(): array
    {
        return Database::all(
            'SELECT c.id, c.name, c.slug, c.icon, c.image,
                    COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p
                    ON p.category_id = c.id AND p.status = "published" AND p.deleted_at IS NULL
             WHERE c.is_active = 1 AND c.deleted_at IS NULL
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.name ASC'
        );
    }

    public function bySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
}
