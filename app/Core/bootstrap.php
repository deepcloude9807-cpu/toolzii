<?php
/**
 * Bootstrap: PSR-4 autoloader (composer-free), env loader, error handling.
 */

declare(strict_types=1);

// ---- Simple PSR-4 autoloader for the App\ namespace ----
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// ---- Global helpers ----
require APP_PATH . '/Core/Helpers.php';

// ---- Environment ----
App\Core\Env::load(BASE_PATH . '/.env');

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// ---- Error handling ----
$debug = filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/php-error.log');

set_exception_handler(function (\Throwable $e) use ($debug) {
    error_log('[Uncaught] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if ($debug) {
        echo '<h1>500 Internal Server Error</h1><pre>'
            . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        $view = APP_PATH . '/Views/pages/500.php';
        if (is_file($view)) { require $view; } else { echo 'Internal Server Error'; }
    }
});
