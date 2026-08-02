<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Seo;
use App\Models\Comparison;

final class ComparisonController extends Controller
{
    public function index(): string
    {
        $this->seo([
            'title'       => 'Product Comparisons | ' . config('app.name'),
            'description' => 'Head-to-head product comparisons with winners, pros, cons and best prices.',
            'canonical'   => url('compare'),
        ]);
        return $this->view('comparison/index', [
            'comparisons' => (new Comparison())->publishedList(30),
        ]);
    }

    public function show(string $slug): string
    {
        $model = new Comparison();
        $comparison = $model->detailBySlug($slug);
        if (!$comparison) {
            $this->abort(404, 'Comparison not found.');
        }
        $model->incrementViews((int) $comparison['id']);

        $this->seo([
            'title'       => $comparison['meta_title'] ?: ($comparison['title'] . ' | ' . config('app.name')),
            'description' => $comparison['meta_description'] ?: str_excerpt($comparison['intro'] ?? '', 160),
            'canonical'   => url('compare/' . $comparison['slug']),
            'schema'      => Seo::jsonLd(Seo::breadcrumb([
                'Home' => url(''),
                'Compare' => url('compare'),
                $comparison['title'] => url('compare/' . $comparison['slug']),
            ])),
        ]);

        return $this->view('comparison/show', ['c' => $comparison]);
    }
}
