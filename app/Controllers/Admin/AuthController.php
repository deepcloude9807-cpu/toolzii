<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\RateLimiter;
use App\Core\Database;
use App\Models\User;

final class AuthController extends Controller
{
    public function login(): string
    {
        if (Auth::check()) {
            redirect('admin/dashboard');
        }

        if ($this->request->method() === 'POST') {
            $email = trim((string) $this->request->input('email', ''));
            $password = (string) $this->request->input('password', '');

            $key = 'login:' . $this->request->ip();
            if (RateLimiter::tooMany($key, 5, 900)) {
                Session::flash('error', 'Too many login attempts. Please wait 15 minutes.');
                redirect('admin/login');
            }

            if (Auth::attempt($email, $password)) {
                RateLimiter::clear($key);
                Session::flash('success', 'Welcome back!');
                redirect('admin/dashboard');
            }
            Session::flash('error', 'Invalid credentials.');
            redirect('admin/login');
        }

        return $this->view('admin/auth/login', [], 'admin/layouts/auth');
    }

    public function logout(): string
    {
        Auth::logout();
        Session::flash('success', 'You have been logged out.');
        redirect('admin/login');
    }

    public function forgot(): string
    {
        if ($this->request->method() === 'POST') {
            $email = trim((string) $this->request->input('email', ''));
            $user = (new User())->findBy('email', $email);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                Database::run(
                    'UPDATE users SET reset_token = ?, reset_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?',
                    [hash('sha256', $token), $user['id']]
                );
                // In production, email this link via SMTP. Logged for dev visibility.
                error_log('[Password reset] ' . url('admin/reset-password/' . $token));
            }
            // Always show the same message (no account enumeration).
            Session::flash('success', 'If that email exists, a reset link has been sent.');
            redirect('admin/login');
        }
        return $this->view('admin/auth/forgot', [], 'admin/layouts/auth');
    }

    public function reset(string $token): string
    {
        $hashed = hash('sha256', $token);
        $user = Database::first(
            'SELECT * FROM users WHERE reset_token = ? AND reset_expires_at > NOW() LIMIT 1',
            [$hashed]
        );
        if (!$user) {
            Session::flash('error', 'This reset link is invalid or has expired.');
            redirect('admin/login');
        }

        if ($this->request->method() === 'POST') {
            $password = (string) $this->request->input('password', '');
            $confirm = (string) $this->request->input('password_confirmation', '');
            if (strlen($password) < 8 || $password !== $confirm) {
                Session::flash('error', 'Passwords must match and be at least 8 characters.');
                redirect('admin/reset-password/' . $token);
            }
            Database::run(
                'UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?',
                [password_hash($password, PASSWORD_DEFAULT), $user['id']]
            );
            Session::flash('success', 'Password updated. Please log in.');
            redirect('admin/login');
        }

        return $this->view('admin/auth/reset', ['token' => $token], 'admin/layouts/auth');
    }
}
