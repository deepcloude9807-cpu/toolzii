<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Banner extends Model
{
    protected string $table = 'banners';

    public function forPosition(string $position, int $limit = 5): array
    {
        return Database::all(
            'SELECT * FROM banners
             WHERE position = ? AND is_active = 1
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (ends_at IS NULL OR ends_at >= NOW())
             ORDER BY sort_order ASC LIMIT ?',
            [$position, $limit]
        );
    }
}
