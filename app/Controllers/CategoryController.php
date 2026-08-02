<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Seo;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

final class CategoryController extends Controller
{
    public function show(string $slug): string
    {
        $category = (new Category())->bySlug($slug);
        if (!$category || (int) $category['is_active'] !== 1) {
            $this->abort(404, 'Category not found.');
        }

        $page = max(1, (int) $this->request->input('page', 1));
        $model = new Product();
        $result = $model->search([
            'category_id' => $category['id'],
            'q'           => $this->request->input('q', ''),
            'brand_id'    => $this->request->input('brand_id', ''),
            'min_price'   => $this->request->input('min_price', ''),
            'max_price'   => $this->request->input('max_price', ''),
            'sort'        => $this->request->input('sort', ''),
        ], $page, config('app.per_page'));

        foreach ($result['data'] as &$p) {
            $p['primary_image'] = $model->primaryImage((int) $p['id']);
        }
        unset($p);

        $this->seo([
            'title'       => $category['meta_title'] ?: ($category['name'] . ' - Best Deals & Reviews | ' . config('app.name')),
            'description' => $category['meta_description'] ?: str_excerpt($category['description'] ?? '', 160),
            'canonical'   => url('category/' . $category['slug']),
            'schema'      => Seo::jsonLd(Seo::breadcrumb([
                'Home' => url(''),
                $category['name'] => url('category/' . $category['slug']),
            ])),
        ]);

        return $this->view('category/show', [
            'category' => $category,
            'result'   => $result,
            'brands'   => (new Brand())->active(),
            'filters'  => $this->request->all(),
        ]);
    }
}
