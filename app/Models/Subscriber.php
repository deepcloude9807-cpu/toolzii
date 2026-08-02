<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Subscriber extends Model
{
    protected string $table = 'subscribers';

    public function subscribe(string $email, string $ipHash): bool
    {
        $exists = Database::value('SELECT COUNT(*) FROM subscribers WHERE email = ?', [$email]);
        if ($exists) {
            return false;
        }
        Database::run(
            'INSERT INTO subscribers (email, token, ip_hash) VALUES (?, ?, ?)',
            [$email, bin2hex(random_bytes(16)), $ipHash]
        );
        return true;
    }
}
