<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Brand extends Model
{
    protected string $table = 'brands';
    protected bool $softDelete = true;

    public function active(): array
    {
        return Database::all(
            'SELECT id, name, slug FROM brands
             WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC'
        );
    }

    public function bySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
}
