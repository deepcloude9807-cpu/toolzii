<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Seo;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Comparison;
use App\Models\Banner;
use App\Models\Setting;

final class HomeController extends Controller
{
    public function index(): string
    {
        $product = new Product();

        $this->seo([
            'title'       => Setting::get('meta_title', config('app.name')),
            'description' => Setting::get('meta_description', ''),
            'canonical'   => url(''),
            'schema'      => Seo::jsonLd([
                '@context' => 'https://schema.org',
                '@type'    => 'WebSite',
                'name'     => config('app.name'),
                'url'      => url(''),
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => url('search?q={query}'),
                    'query-input' => 'required name=query',
                ],
            ]),
        ]);

        return $this->view('home/index', [
            'heroBanners'   => (new Banner())->forPosition('homepage', 3),
            'trending'      => $product->trending(8),
            'deals'         => $product->deals(8),
            'categories'    => (new Category())->withCounts(),
            'featured'      => $product->featured(8),
            'comparisons'   => (new Comparison())->publishedList(4),
            'latestPosts'   => (new Blog())->latest(3),
            'attachPrimary' => true,
        ]);
    }
}
