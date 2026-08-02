<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    protected string $table = 'users';
    protected bool $softDelete = true;

    public function roleCounts(): array
    {
        $rows = \App\Core\Database::all(
            'SELECT role, COUNT(*) c FROM users WHERE deleted_at IS NULL GROUP BY role'
        );
        $out = ['admin' => 0, 'editor' => 0, 'author' => 0];
        foreach ($rows as $r) {
            $out[$r['role']] = (int) $r['c'];
        }
        return $out;
    }
}
