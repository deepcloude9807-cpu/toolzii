<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Upload;
use App\Core\Database;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

final class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole('admin', 'editor', 'author');
    }

    public function index(): string
    {
        $model = new Product();
        $page = max(1, (int) $this->request->input('page', 1));
        $q = trim((string) $this->request->input('q', ''));
        $where = '1=1';
        $params = [];
        if ($q !== '') {
            $where = 'name LIKE ?';
            $params[] = '%' . $q . '%';
        }
        $result = $model->paginate($page, 15, $where, $params, 'id DESC');
        foreach ($result['data'] as &$p) {
            $p['primary_image'] = $model->primaryImage((int) $p['id']);
        }
        unset($p);

        return $this->view('admin/products/index', ['result' => $result, 'q' => $q], 'admin/layouts/app');
    }

    public function create(): string
    {
        if ($this->request->method() === 'POST') {
            return $this->persist(null);
        }
        return $this->form(null);
    }

    public function edit(string $id): string
    {
        $product = (new Product())->find((int) $id);
        if (!$product) {
            $this->abort(404);
        }
        if ($this->request->method() === 'POST') {
            return $this->persist($product);
        }
        return $this->form($product);
    }

    private function form(?array $product): string
    {
        $images = $product
            ? Database::all('SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order', [$product['id']])
            : [];
        return $this->view('admin/products/form', [
            'product'    => $product,
            'images'     => $images,
            'categories' => (new Category())->all('name ASC'),
            'brands'     => (new Brand())->active(),
        ], 'admin/layouts/app');
    }

    private function persist(?array $existing): string
    {
        $data = $this->request->all();
        $v = new Validator($data);
        if (!$v->validate([
            'name'  => 'required|max:200',
            'price' => 'numeric',
            'original_price' => 'numeric',
        ])) {
            Session::flash('error', $v->firstError());
            Session::flash('_old', $data);
            redirect($existing ? 'admin/products/' . $existing['id'] . '/edit' : 'admin/products/create');
        }

        $slug = trim((string) ($data['slug'] ?? '')) ?: slugify((string) $data['name']);
        $slug = $this->uniqueSlug($slug, $existing['id'] ?? null);

        $price = $data['price'] !== '' ? (float) $data['price'] : null;
        $original = $data['original_price'] !== '' ? (float) $data['original_price'] : null;
        $discount = ($price && $original && $original > $price)
            ? (int) round((($original - $price) / $original) * 100)
            : ($data['discount'] !== '' ? (int) $data['discount'] : null);

        $fields = [
            'category_id'     => $data['category_id'] ?: null,
            'brand_id'        => $data['brand_id'] ?: null,
            'name'            => trim((string) $data['name']),
            'slug'            => $slug,
            'short_description' => trim((string) ($data['short_description'] ?? '')),
            'long_description' => (string) ($data['long_description'] ?? ''),
            'features'        => $this->jsonList($data['features'] ?? ''),
            'specifications'  => $this->jsonSpecs($data['spec_key'] ?? [], $data['spec_val'] ?? []),
            'pros'            => $this->jsonList($data['pros'] ?? ''),
            'cons'            => $this->jsonList($data['cons'] ?? ''),
            'faq'             => $this->jsonFaq($data['faq_q'] ?? [], $data['faq_a'] ?? []),
            'rating'          => (float) ($data['rating'] ?? 0),
            'price'           => $price,
            'original_price'  => $original,
            'discount'        => $discount,
            'amazon_link'     => trim((string) ($data['amazon_link'] ?? '')) ?: null,
            'flipkart_link'   => trim((string) ($data['flipkart_link'] ?? '')) ?: null,
            'meesho_link'     => trim((string) ($data['meesho_link'] ?? '')) ?: null,
            'custom_link'     => trim((string) ($data['custom_link'] ?? '')) ?: null,
            'is_featured'     => isset($data['is_featured']) ? 1 : 0,
            'is_trending'     => isset($data['is_trending']) ? 1 : 0,
            'is_deal'         => isset($data['is_deal']) ? 1 : 0,
            'is_bestseller'   => isset($data['is_bestseller']) ? 1 : 0,
            'is_new'          => isset($data['is_new']) ? 1 : 0,
            'status'          => in_array($data['status'] ?? 'draft', ['draft', 'published', 'scheduled'], true) ? $data['status'] : 'draft',
            'published_at'    => ($data['status'] ?? '') === 'published' ? date('Y-m-d H:i:s') : ($data['published_at'] ?: null),
            'meta_title'      => trim((string) ($data['meta_title'] ?? '')) ?: null,
            'meta_description' => trim((string) ($data['meta_description'] ?? '')) ?: null,
        ];

        $model = new Product();
        if ($existing) {
            $model->update((int) $existing['id'], $fields);
            $productId = (int) $existing['id'];
        } else {
            $fields['created_by'] = Auth::id();
            $productId = $model->create($fields);
        }

        // Gallery upload
        if (!empty($_FILES['gallery']['name'][0])) {
            $paths = Upload::gallery($_FILES['gallery'], 'products', 8);
            $hasPrimary = (int) Database::value('SELECT COUNT(*) FROM product_images WHERE product_id = ? AND is_primary = 1', [$productId]) > 0;
            foreach ($paths as $i => $path) {
                Database::run(
                    'INSERT INTO product_images (product_id, path, alt, is_primary, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$productId, $path, $fields['name'], (!$hasPrimary && $i === 0) ? 1 : 0, $i]
                );
            }
        }

        Session::flash('success', 'Product saved successfully.');
        redirect('admin/products');
    }

    public function destroy(string $id): string
    {
        (new Product())->delete((int) $id);
        Session::flash('success', 'Product moved to trash.');
        redirect('admin/products');
    }

    /** CSV bulk import: columns name,category,brand,price,original_price,amazon_link,short_description,status */
    public function import(): string
    {
        if ($this->request->method() === 'POST' && !empty($_FILES['csv']['tmp_name']) && is_uploaded_file($_FILES['csv']['tmp_name'])) {
            $handle = fopen($_FILES['csv']['tmp_name'], 'r');
            if ($handle === false) {
                Session::flash('error', 'Could not read CSV.');
                redirect('admin/products/import');
            }
            $header = fgetcsv($handle);
            $map = $header ? array_flip(array_map('trim', $header)) : [];
            $imported = 0;
            $model = new Product();
            while (($row = fgetcsv($handle)) !== false) {
                $get = static fn (string $k) => isset($map[$k]) ? trim((string) ($row[$map[$k]] ?? '')) : '';
                $name = $get('name');
                if ($name === '') {
                    continue;
                }
                $categoryId = $get('category') !== ''
                    ? Database::value('SELECT id FROM categories WHERE slug = ? OR name = ? LIMIT 1', [slugify($get('category')), $get('category')])
                    : null;
                $brandId = $get('brand') !== ''
                    ? Database::value('SELECT id FROM brands WHERE slug = ? OR name = ? LIMIT 1', [slugify($get('brand')), $get('brand')])
                    : null;
                $model->create([
                    'category_id'      => $categoryId ?: null,
                    'brand_id'         => $brandId ?: null,
                    'name'             => $name,
                    'slug'             => $this->uniqueSlug(slugify($name), null),
                    'short_description' => $get('short_description'),
                    'price'            => $get('price') !== '' ? (float) $get('price') : null,
                    'original_price'   => $get('original_price') !== '' ? (float) $get('original_price') : null,
                    'amazon_link'      => $get('amazon_link') ?: null,
                    'status'           => in_array($get('status'), ['draft', 'published'], true) ? $get('status') : 'draft',
                    'published_at'     => $get('status') === 'published' ? date('Y-m-d H:i:s') : null,
                    'created_by'       => Auth::id(),
                ]);
                $imported++;
            }
            fclose($handle);
            Session::flash('success', "Imported {$imported} products.");
            redirect('admin/products');
        }
        return $this->view('admin/products/import', [], 'admin/layouts/app');
    }

    private function uniqueSlug(string $slug, ?int $ignoreId): string
    {
        $base = $slug;
        $i = 1;
        while (true) {
            $exists = Database::value(
                'SELECT id FROM products WHERE slug = ?' . ($ignoreId ? ' AND id <> ?' : ''),
                $ignoreId ? [$slug, $ignoreId] : [$slug]
            );
            if (!$exists) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }

    private function jsonList(string $raw): ?string
    {
        $items = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: [])));
        return $items ? json_encode($items) : null;
    }

    private function jsonSpecs(array $keys, array $vals): ?string
    {
        $out = [];
        foreach ($keys as $i => $k) {
            $k = trim((string) $k);
            $val = trim((string) ($vals[$i] ?? ''));
            if ($k !== '') {
                $out[$k] = $val;
            }
        }
        return $out ? json_encode($out) : null;
    }

    private function jsonFaq(array $qs, array $as): ?string
    {
        $out = [];
        foreach ($qs as $i => $q) {
            $q = trim((string) $q);
            $a = trim((string) ($as[$i] ?? ''));
            if ($q !== '') {
                $out[] = ['q' => $q, 'a' => $a];
            }
        }
        return $out ? json_encode($out) : null;
    }
}
