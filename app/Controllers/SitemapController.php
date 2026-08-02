<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

final class SitemapController extends Controller
{
    public function index(): string
    {
        header('Content-Type: application/xml; charset=utf-8');
        $urls = [
            ['loc' => url(''), 'priority' => '1.0'],
            ['loc' => url('blog'), 'priority' => '0.7'],
            ['loc' => url('compare'), 'priority' => '0.7'],
        ];
        foreach (Database::all('SELECT slug, updated_at FROM categories WHERE is_active = 1 AND deleted_at IS NULL') as $c) {
            $urls[] = ['loc' => url('category/' . $c['slug']), 'lastmod' => $c['updated_at'], 'priority' => '0.8'];
        }
        foreach (Database::all('SELECT slug, updated_at FROM products WHERE status = "published" AND deleted_at IS NULL') as $p) {
            $urls[] = ['loc' => url('product/' . $p['slug']), 'lastmod' => $p['updated_at'], 'priority' => '0.9'];
        }
        foreach (Database::all('SELECT slug, updated_at FROM blog_posts WHERE status = "published" AND deleted_at IS NULL') as $b) {
            $urls[] = ['loc' => url('blog/' . $b['slug']), 'lastmod' => $b['updated_at'], 'priority' => '0.6'];
        }
        foreach (Database::all('SELECT slug, updated_at FROM comparisons WHERE status = "published" AND deleted_at IS NULL') as $c) {
            $urls[] = ['loc' => url('compare/' . $c['slug']), 'lastmod' => $c['updated_at'], 'priority' => '0.6'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n    <loc>" . e($u['loc']) . "</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= '    <lastmod>' . date('Y-m-d', strtotime((string) $u['lastmod'])) . "</lastmod>\n";
            }
            $xml .= '    <priority>' . $u['priority'] . "</priority>\n  </url>\n";
        }
        $xml .= '</urlset>';
        return $xml;
    }

    public function robots(): string
    {
        header('Content-Type: text/plain; charset=utf-8');
        return "User-agent: *\n"
            . "Disallow: /admin\n"
            . "Disallow: /go/\n"
            . "Disallow: /search\n"
            . "Allow: /\n\n"
            . 'Sitemap: ' . url('sitemap.xml') . "\n";
    }
}
