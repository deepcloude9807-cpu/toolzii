<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Review extends Model
{
    protected string $table = 'reviews';

    public function approvedFor(int $productId): array
    {
        return Database::all(
            'SELECT author_name, rating, title, body, created_at
             FROM reviews WHERE product_id = ? AND is_approved = 1 ORDER BY created_at DESC',
            [$productId]
        );
    }
}
