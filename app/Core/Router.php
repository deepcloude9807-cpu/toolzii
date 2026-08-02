<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Lightweight regex router with named params {slug}.
 */
final class Router
{
    /** @var array<string, array<int, array{pattern:string, handler:mixed, params:array}>> */
    private array $routes = [];

    public function get(string $path, mixed $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, mixed $handler): void { $this->add('POST', $path, $handler); }
    public function put(string $path, mixed $handler): void { $this->add('PUT', $path, $handler); }
    public function delete(string $path, mixed $handler): void { $this->add('DELETE', $path, $handler); }

    /** Register GET+POST at once. */
    public function match(array $methods, string $path, mixed $handler): void
    {
        foreach ($methods as $m) {
            $this->add(strtoupper($m), $path, $handler);
        }
    }

    private function add(string $method, string $path, mixed $handler): void
    {
        $params = [];
        $pattern = preg_replace_callback('~\{([a-zA-Z_][a-zA-Z0-9_]*)\}~', function ($m) use (&$params) {
            $params[] = $m[1];
            return '([^/]+)';
        }, rtrim($path, '/') ?: '/');
        $pattern = '~^' . $pattern . '$~';
        $this->routes[$method][] = ['pattern' => $pattern, 'handler' => $handler, 'params' => $params];
    }

    /**
     * @return array{0:mixed,1:array}|null  [handler, namedParams]
     */
    public function dispatch(string $method, string $uri): ?array
    {
        $uri = rtrim($uri, '/') ?: '/';
        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $named = [];
                foreach ($route['params'] as $i => $name) {
                    $named[$name] = $matches[$i] ?? null;
                }
                return [$route['handler'], $named];
            }
        }
        return null;
    }
}
