<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Schema.org JSON-LD builders for rich results.
 */
final class Seo
{
    public static function jsonLd(array $data): string
    {
        return '<script type="application/ld+json">'
            . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>';
    }

    public static function product(array $p): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => $p['name'],
            'description' => str_excerpt($p['short_description'] ?? $p['long_description'] ?? '', 300),
            'sku'      => (string) $p['id'],
            'brand'    => ['@type' => 'Brand', 'name' => $p['brand_name'] ?? 'Generic'],
        ];
        if (!empty($p['og_image']) || !empty($p['image'])) {
            $schema['image'] = uploaded($p['og_image'] ?? $p['image']);
        }
        if (!empty($p['price'])) {
            $schema['offers'] = [
                '@type'         => 'Offer',
                'priceCurrency' => 'INR',
                'price'         => (string) $p['price'],
                'availability'  => 'https://schema.org/InStock',
                'url'           => url('product/' . $p['slug']),
            ];
        }
        if (!empty($p['rating']) && (float) $p['rating'] > 0) {
            $schema['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) $p['rating'],
                'reviewCount' => (string) max(1, (int) ($p['rating_count'] ?? 1)),
            ];
        }
        return $schema;
    }

    public static function faq(array $faqs): ?array
    {
        if (!$faqs) {
            return null;
        }
        $items = [];
        foreach ($faqs as $f) {
            if (empty($f['q'])) {
                continue;
            }
            $items[] = [
                '@type'          => 'Question',
                'name'           => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a'] ?? ''],
            ];
        }
        return $items ? ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items] : null;
    }

    public static function breadcrumb(array $crumbs): array
    {
        $items = [];
        $pos = 1;
        foreach ($crumbs as $label => $href) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $label,
                'item'     => $href,
            ];
        }
        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    public static function article(array $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'BlogPosting',
            'headline' => $post['title'],
            'image'    => uploaded($post['featured_image'] ?? null),
            'datePublished' => $post['published_at'] ?? $post['created_at'],
            'dateModified'  => $post['updated_at'] ?? $post['created_at'],
            'author'   => ['@type' => 'Person', 'name' => $post['author_name'] ?? 'ToolzyNet'],
            'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
        ];
    }
}
