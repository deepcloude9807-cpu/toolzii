<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

final class Setting extends Model
{
    protected string $table = 'settings';
    private static ?array $cache = null;

    public static function allAsArray(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $rows = Database::all('SELECT `key`, `value` FROM settings');
        $out = [];
        foreach ($rows as $r) {
            $out[$r['key']] = $r['value'];
        }
        return self::$cache = $out;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::allAsArray()[$key] ?? $default;
    }

    public static function put(string $key, ?string $value, string $group = 'general'): void
    {
        Database::run(
            'INSERT INTO settings (`key`, `value`, `group`) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [$key, $value, $group]
        );
        self::$cache = null;
    }
}
