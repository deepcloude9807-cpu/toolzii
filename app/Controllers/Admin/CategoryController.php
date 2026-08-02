<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Upload;
use App\Core\Database;
use App\Models\Category;

final class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole('admin', 'editor');
    }

    public function index(): string
    {
        return $this->view('admin/categories/index', [
            'categories' => (new Category())->all('sort_order ASC, name ASC'),
        ], 'admin/layouts/app');
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
        $category = (new Category())->find((int) $id);
        if (!$category) {
            $this->abort(404);
        }
        if ($this->request->method() === 'POST') {
            return $this->persist($category);
        }
        return $this->form($category);
    }

    private function form(?array $category): string
    {
        return $this->view('admin/categories/form', [
            'category' => $category,
            'parents'  => (new Category())->all('name ASC'),
        ], 'admin/layouts/app');
    }

    private function persist(?array $existing): string
    {
        $data = $this->request->all();
        $v = new Validator($data);
        if (!$v->validate(['name' => 'required|max:150'])) {
            Session::flash('error', $v->firstError());
            redirect($existing ? 'admin/categories/' . $existing['id'] . '/edit' : 'admin/categories/create');
        }

        $slug = trim((string) ($data['slug'] ?? '')) ?: slugify((string) $data['name']);
        $fields = [
            'parent_id'       => $data['parent_id'] ?: null,
            'name'            => trim((string) $data['name']),
            'slug'            => $slug,
            'description'     => trim((string) ($data['description'] ?? '')),
            'icon'            => trim((string) ($data['icon'] ?? '')) ?: null,
            'meta_title'      => trim((string) ($data['meta_title'] ?? '')) ?: null,
            'meta_description' => trim((string) ($data['meta_description'] ?? '')) ?: null,
            'sort_order'      => (int) ($data['sort_order'] ?? 0),
            'is_active'       => isset($data['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['image']['name'])) {
            if ($path = Upload::image($_FILES['image'], 'brands')) {
                $fields['image'] = $path;
            }
        }

        $model = new Category();
        if ($existing) {
            $model->update((int) $existing['id'], $fields);
        } else {
            $model->create($fields);
        }
        Session::flash('success', 'Category saved.');
        redirect('admin/categories');
    }

    public function destroy(string $id): string
    {
        (new Category())->delete((int) $id);
        Session::flash('success', 'Category deleted.');
        redirect('admin/categories');
    }
}
