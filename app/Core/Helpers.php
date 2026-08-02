<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\Session;
use App\Core\Csrf;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    /** Dot-notation access to config arrays: config('app.name'). */
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];
        $parts = explode('.', $key);
        $file = array_shift($parts);
        if (!isset($cache[$file])) {
            $path = BASE_PATH . '/config/' . $file . '.php';
            $cache[$file] = is_file($path) ? require $path : [];
        }
        $value = $cache[$file];
        foreach ($parts as $p) {
            if (is_array($value) && array_key_exists($p, $value)) {
                $value = $value[$p];
            } else {
                return $default;
            }
        }
        return $value;
    }
}

if (!function_exists('e')) {
    /** HTML-escape (XSS protection) for output. */
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) env('APP_URL', ''), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('uploaded')) {
    function uploaded(?string $path, string $fallback = 'img/placeholder.svg'): string
    {
        if (!$path) {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return url('uploads/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        $location = str_starts_with($path, 'http') ? $path : url($path);
        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return (string) (Session::flashGet('_old')[$key] ?? $default);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text) ?? '';
        $text = trim($text, '-');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text) ?? '';
        return $text !== '' ? $text : 'n-a';
    }
}

if (!function_exists('money')) {
    function money(float|int|null $amount): string
    {
        return '₹' . number_format((float) $amount, 0);
    }
}

if (!function_exists('str_excerpt')) {
    function str_excerpt(?string $text, int $length = 160): string
    {
        $text = trim(strip_tags((string) $text));
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 1) . '…';
    }
}
