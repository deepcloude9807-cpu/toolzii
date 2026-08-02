<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Native PHP template renderer with a shared layout.
 */
final class View
{
    private static array $shared = [];

    /** Data available to every view (site settings, nav categories, etc.). */
    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $data = array_merge(self::$shared, $data);
        $content = self::capture($template, $data);

        if ($layout === null) {
            return $content;
        }
        $data['content'] = $content;
        return self::capture($layout, $data);
    }

    private static function capture(string $template, array $data): string
    {
        $file = APP_PATH . '/Views/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View not found: {$template}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    /** Render a partial inline (used inside templates). */
    public static function partial(string $template, array $data = []): void
    {
        echo self::capture($template, array_merge(self::$shared, $data));
    }
}
