<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

final class SearchController extends Controller
{
    /** Full search results page. */
    public function index(): string
    {
        $term = trim((string) $this->request->input('q', ''));
        $page = max(1, (int) $this->request->input('page', 1));
        $model = new Product();

        $result = $model->search([
            'q'           => $term,
            'category_id' => $this->request->input('category_id', ''),
            'brand_id'    => $this->request->input('brand_id', ''),
            'min_price'   => $this->request->input('min_price', ''),
            'max_price'   => $this->request->input('max_price', ''),
            'sort'        => $this->request->input('sort', ''),
        ], $page, config('app.per_page'));

        foreach ($result['data'] as &$p) {
            $p['primary_image'] = $model->primaryImage((int) $p['id']);
        }
        unset($p);

        if ($term !== '') {
            Database::run(
                'INSERT INTO search_logs (term, results_count, ip_hash) VALUES (?, ?, ?)',
                [mb_substr($term, 0, 190), $result['total'], hash('sha256', $this->request->ip())]
            );
        }

        $this->seo([
            'title'       => ($term !== '' ? "Search: {$term}" : 'Search Products') . ' | ' . config('app.name'),
            'description' => 'Search results on ToolzyNet.',
            'robots'      => 'noindex, follow',
        ]);

        return $this->view('product/search', [
            'term'       => $term,
            'result'     => $result,
            'categories' => (new Category())->menu(50),
            'brands'     => (new Brand())->active(),
            'filters'    => $this->request->all(),
        ]);
    }

    /** AJAX autocomplete -> JSON. */
    public function suggest(): string
    {
        $term = trim((string) $this->request->input('q', ''));
        if (mb_strlen($term) < 2) {
            return $this->json(['results' => []]);
        }
        $items = (new Product())->suggest($term, 8);
        $results = array_map(static fn ($p) => [
            'name' => $p['name'],
            'url'  => url('product/' . $p['slug']),
        ], $items);
        return $this->json(['results' => $results]);
    }

    /** AJAX faceted filter (returns rendered cards + meta) -> JSON. */
    public function filter(): string
    {
        $page = max(1, (int) $this->request->input('page', 1));
        $model = new Product();
        $result = $model->search([
            'q'           => $this->request->input('q', ''),
            'category_id' => $this->request->input('category_id', ''),
            'brand_id'    => $this->request->input('brand_id', ''),
            'min_price'   => $this->request->input('min_price', ''),
            'max_price'   => $this->request->input('max_price', ''),
            'sort'        => $this->request->input('sort', ''),
        ], $page, config('app.per_page'));

        $cards = '';
        foreach ($result['data'] as $p) {
            $p['primary_image'] = $model->primaryImage((int) $p['id']);
            $cards .= \App\Core\View::render('partials/product-card', ['product' => $p], null);
        }

        return $this->json([
            'html'      => $cards,
            'total'     => $result['total'],
            'page'      => $result['page'],
            'last_page' => $result['last_page'],
        ]);
    }
}
