<?php

declare(strict_types=1);

namespace App\Core;

/**
 * File-based fixed-window rate limiter. Good enough for form/login abuse control
 * without extra infra; swap the store for Redis at scale.
 */
final class RateLimiter
{
    public static function tooMany(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $file = self::path($key);
        $now = time();
        $data = ['count' => 0, 'reset' => $now + $windowSeconds];
        if (is_file($file)) {
            $decoded = json_decode((string) file_get_contents($file), true);
            if (is_array($decoded) && ($decoded['reset'] ?? 0) > $now) {
                $data = $decoded;
            }
        }
        $data['count']++;
        @file_put_contents($file, json_encode($data), LOCK_EX);
        return $data['count'] > $maxAttempts;
    }

    public static function clear(string $key): void
    {
        @unlink(self::path($key));
    }

    private static function path(string $key): string
    {
        $dir = STORAGE_PATH . '/cache/ratelimit';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir . '/' . hash('sha256', $key) . '.json';
    }
}
