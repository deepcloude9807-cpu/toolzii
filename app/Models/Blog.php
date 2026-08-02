<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Blog extends Model
{
    protected string $table = 'blog_posts';
    protected bool $softDelete = true;

    private const SELECT = 'b.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name';
    private const JOINS = 'FROM blog_posts b
                           LEFT JOIN categories c ON c.id = b.category_id
                           LEFT JOIN users u ON u.id = b.author_id';

    public function published(int $page, int $perPage, ?int $categoryId = null): array
    {
        $where = 'b.status = "published" AND b.deleted_at IS NULL';
        $params = [];
        if ($categoryId) {
            $where .= ' AND b.category_id = ?';
            $params[] = $categoryId;
        }
        $total = (int) Database::value('SELECT COUNT(*) FROM blog_posts b WHERE ' . $where, $params);
        $offset = (max(1, $page) - 1) * $perPage;
        $rows = Database::all(
            'SELECT ' . self::SELECT . ' ' . self::JOINS . " WHERE {$where} ORDER BY b.published_at DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        return [
            'data' => $rows,
            'total' => $total,
            'page' => max(1, $page),
            'last_page' => (int) max(1, ceil($total / $perPage)),
        ];
    }

    public function latest(int $limit = 3): array
    {
        return Database::all(
            'SELECT ' . self::SELECT . ' ' . self::JOINS .
            ' WHERE b.status = "published" AND b.deleted_at IS NULL ORDER BY b.published_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function detailBySlug(string $slug): ?array
    {
        $row = Database::first(
            'SELECT ' . self::SELECT . ' ' . self::JOINS . ' WHERE b.slug = ? AND b.status = "published" AND b.deleted_at IS NULL',
            [$slug]
        );
        if ($row) {
            $row['tags'] = $row['tags'] ? json_decode((string) $row['tags'], true) : [];
        }
        return $row;
    }

    public function related(int $categoryId, int $excludeId, int $limit = 3): array
    {
        return Database::all(
            'SELECT ' . self::SELECT . ' ' . self::JOINS .
            ' WHERE b.status = "published" AND b.deleted_at IS NULL AND b.category_id = ? AND b.id <> ? ORDER BY b.published_at DESC LIMIT ?',
            [$categoryId, $excludeId, $limit]
        );
    }

    public function incrementViews(int $id): void
    {
        Database::run('UPDATE blog_posts SET views = views + 1 WHERE id = ?', [$id]);
    }

    public function approvedComments(int $postId): array
    {
        return Database::all(
            'SELECT author_name, body, created_at FROM blog_comments WHERE post_id = ? AND is_approved = 1 ORDER BY created_at DESC',
            [$postId]
        );
    }
}
