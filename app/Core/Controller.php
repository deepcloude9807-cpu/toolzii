<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function view(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        return View::render($template, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    protected function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        $view = APP_PATH . "/Views/pages/{$code}.php";
        if (is_file($view)) {
            echo View::render("pages/{$code}", ['message' => $message]);
        } else {
            echo $message ?: "Error {$code}";
        }
        exit;
    }

    protected function seo(array $meta): void
    {
        View::share('seo', array_merge([
            'title'       => config('app.name'),
            'description' => '',
            'canonical'   => url(ltrim($this->request->uri(), '/')),
            'robots'      => 'index, follow',
            'og_image'    => asset('img/og-default.svg'),
            'type'        => 'website',
            'schema'      => null,
        ], $meta));
    }
}
