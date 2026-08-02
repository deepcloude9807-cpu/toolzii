<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Seo;
use App\Core\Session;
use App\Core\Validator;
use App\Core\RateLimiter;
use App\Core\Database;
use App\Models\Product;
use App\Models\Review;
use App\Models\AffiliateClick;

final class ProductController extends Controller
{
    public function show(string $slug): string
    {
        $model = new Product();
        $product = $model->detailBySlug($slug);
        if (!$product) {
            $this->abort(404, 'Product not found.');
        }

        $model->incrementViews((int) $product['id']);

        $primary = $product['images'][0]['path'] ?? null;
        $related = $model->related((int) $product['category_id'], (int) $product['id'], 4);
        foreach ($related as &$r) {
            $r['primary_image'] = $model->primaryImage((int) $r['id']);
        }
        unset($r);

        $schemas = [Seo::product(array_merge($product, ['image' => $primary]))];
        if ($faq = Seo::faq($product['faq'] ?? [])) {
            $schemas[] = $faq;
        }
        $schemas[] = Seo::breadcrumb([
            'Home' => url(''),
            $product['category_name'] ?? 'Products' => url('category/' . ($product['category_slug'] ?? '')),
            $product['name'] => url('product/' . $product['slug']),
        ]);

        $this->seo([
            'title'       => $product['meta_title'] ?: ($product['name'] . ' | ' . config('app.name')),
            'description' => $product['meta_description'] ?: str_excerpt($product['short_description'] ?? '', 160),
            'canonical'   => url('product/' . $product['slug']),
            'og_image'    => uploaded($product['og_image'] ?: $primary),
            'type'        => 'product',
            'schema'      => implode("\n", array_map([Seo::class, 'jsonLd'], $schemas)),
        ]);

        return $this->view('product/show', [
            'product' => $product,
            'related' => $related,
            'reviews' => (new Review())->approvedFor((int) $product['id']),
        ]);
    }

    /** Affiliate redirect + click counter: /go/{id}?partner=amazon */
    public function track(string $id): string
    {
        $productId = (int) $id;
        $partner = strtolower((string) $this->request->input('partner', 'amazon'));
        if (!in_array($partner, config('app.affiliate_partners'), true)) {
            $partner = 'amazon';
        }

        $product = (new Product())->find($productId);
        if (!$product) {
            $this->abort(404);
        }

        $column = $partner === 'custom' ? 'custom_link' : $partner . '_link';
        $target = $product[$column] ?? '';
        if (!$target || !filter_var($target, FILTER_VALIDATE_URL)) {
            $this->abort(404, 'Affiliate link unavailable.');
        }

        (new AffiliateClick())->record(
            $productId,
            $partner,
            hash('sha256', $this->request->ip()),
            $this->request->userAgent(),
            $_SERVER['HTTP_REFERER'] ?? null
        );

        header('Location: ' . $target, true, 302);
        exit;
    }

    /** Visitor review submission (moderated). */
    public function review(string $slug): string
    {
        $product = (new Product())->findBy('slug', $slug);
        if (!$product) {
            $this->abort(404);
        }

        if (RateLimiter::tooMany('review:' . $this->request->ip(), 5, 3600)) {
            Session::flash('error', 'Too many submissions. Please try again later.');
            redirect('product/' . $slug);
        }

        $v = new Validator($this->request->all());
        if (!$v->validate([
            'author_name' => 'required|max:120',
            'rating'      => 'required|numeric',
            'body'        => 'required|min:5|max:2000',
        ])) {
            Session::flash('error', $v->firstError());
            redirect('product/' . $slug . '#reviews');
        }

        $rating = min(5, max(1, (int) $this->request->input('rating', 5)));
        Database::run(
            'INSERT INTO reviews (product_id, author_name, author_email, rating, title, body, is_approved)
             VALUES (?, ?, ?, ?, ?, ?, 0)',
            [
                $product['id'],
                trim((string) $this->request->input('author_name')),
                trim((string) $this->request->input('author_email', '')),
                $rating,
                trim((string) $this->request->input('title', '')),
                trim((string) $this->request->input('body')),
            ]
        );

        Session::flash('success', 'Thanks! Your review will appear after moderation.');
        redirect('product/' . $slug . '#reviews');
    }
}
