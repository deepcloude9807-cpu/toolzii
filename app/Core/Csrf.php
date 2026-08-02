<?php

declare(strict_types=1);

namespace App\Core;

/**
 * CSRF token generation & verification (per-session, hash_equals).
 */
final class Csrf
{
    public static function token(): string
    {
        if (!Session::has('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return (string) Session::get('_csrf_token');
    }

    public static function check(?string $token): bool
    {
        $stored = Session::get('_csrf_token');
        return is_string($stored) && is_string($token) && hash_equals($stored, $token);
    }

    /** Verify a mutating request or abort with 419. */
    public static function verifyRequest(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            if (!self::check($token)) {
                http_response_code(419);
                exit('419 - CSRF token mismatch.');
            }
        }
    }
}
