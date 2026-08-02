<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Comparison extends Model
{
    protected string $table = 'comparisons';
    protected bool $softDelete = true;

    public function publishedList(int $limit = 12): array
    {
        return Database::all(
            'SELECT id, title, slug, intro, views FROM comparisons
             WHERE status = "published" AND deleted_at IS NULL ORDER BY views DESC, created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function detailBySlug(string $slug): ?array
    {
        $row = $this->findBy('slug', $slug);
        if (!$row || $row['status'] !== 'published') {
            return null;
        }
        $row['comparison_table'] = $row['comparison_table']
            ? json_decode((string) $row['comparison_table'], true) : [];
        $pm = new Product();
        $row['product_a'] = $row['product_a_id'] ? $pm->find((int) $row['product_a_id']) : null;
        $row['product_b'] = $row['product_b_id'] ? $pm->find((int) $row['product_b_id']) : null;
        if ($row['product_a']) {
            $row['product_a']['image'] = $pm->primaryImage((int) $row['product_a']['id']);
        }
        if ($row['product_b']) {
            $row['product_b']['image'] = $pm->primaryImage((int) $row['product_b']['id']);
        }
        return $row;
    }

    public function incrementViews(int $id): void
    {
        Database::run('UPDATE comparisons SET views = views + 1 WHERE id = ?', [$id]);
    }
}
