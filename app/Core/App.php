<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;
use App\Models\Category;

/**
 * Application kernel: boots session, loads shared view data, dispatches routes.
 */
final class App
{
    private Router $router;
    private Request $request;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        Session::start();
        $this->registerRoutes();
        $this->shareGlobals();
    }

    private function registerRoutes(): void
    {
        $router = $this->router;
        require BASE_PATH . '/config/routes.php';
    }

    private function shareGlobals(): void
    {
        try {
            View::share('settings', Setting::allAsArray());
            View::share('navCategories', (new Category())->menu());
        } catch (\Throwable $e) {
            // DB may be unconfigured on first run; degrade gracefully.
            View::share('settings', []);
            View::share('navCategories', []);
            error_log('[App] shareGlobals: ' . $e->getMessage());
        }
        View::share('flash', [
            'success' => Session::flashGet('success'),
            'error'   => Session::flashGet('error'),
        ]);
        View::share('errors', Session::flashGet('_errors') ?: []);
        View::share('csrf', Csrf::token());
    }

    public function run(): void
    {
        // Maintenance mode (front-end only)
        $this->handleMaintenance();

        Csrf::verifyRequest();

        $match = $this->router->dispatch($this->request->method(), $this->request->uri());

        if ($match === null) {
            http_response_code(404);
            echo View::render('pages/404', []);
            return;
        }

        [$handler, $params] = $match;
        echo $this->call($handler, $params);
    }

    private function handleMaintenance(): void
    {
        $uri = $this->request->uri();
        if (str_starts_with($uri, '/admin')) {
            return;
        }
        try {
            if (Setting::get('maintenance_mode') === '1') {
                http_response_code(503);
                header('Retry-After: 3600');
                echo View::render('pages/maintenance', [], null);
                exit;
            }
        } catch (\Throwable) {
            // ignore when settings table absent
        }
    }

    /** @param array<string,mixed> $params */
    private function call(mixed $handler, array $params): string
    {
        // "Controller@method" or "Admin\Controller@method"
        [$class, $method] = explode('@', $handler);
        $fqcn = 'App\\Controllers\\' . $class;
        if (!class_exists($fqcn)) {
            throw new \RuntimeException("Controller not found: {$fqcn}");
        }
        $controller = new $fqcn();
        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method not found: {$fqcn}::{$method}");
        }
        $result = $controller->{$method}(...array_values($params));
        return is_string($result) ? $result : '';
    }
}
