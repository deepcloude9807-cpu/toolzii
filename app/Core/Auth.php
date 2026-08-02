<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Session-based authentication with roles (admin, editor, author).
 */
final class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $user = (new User())->findBy('email', $email);
        if (!$user || (int) ($user['is_active'] ?? 0) !== 1) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        // Rehash if algorithm/params changed
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            (new User())->update((int) $user['id'], [
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        }
        Session::set('auth_user', [
            'id'    => (int) $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);
        session_regenerate_id(true);
        (new User())->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        return true;
    }

    public static function check(): bool
    {
        return Session::has('auth_user');
    }

    public static function user(): ?array
    {
        return Session::get('auth_user');
    }

    public static function id(): ?int
    {
        return isset($_SESSION['auth_user']['id']) ? (int) $_SESSION['auth_user']['id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['auth_user']['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    public static function logout(): void
    {
        Session::forget('auth_user');
        session_regenerate_id(true);
    }

    /** Guard: redirect to login if not authenticated. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please log in to continue.');
            redirect('admin/login');
        }
    }

    /** Guard: require one of the given roles or abort 403. */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            exit('403 - You do not have permission to access this area.');
        }
    }
}
