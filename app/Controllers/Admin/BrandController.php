<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Upload;
use App\Models\Brand;

final class BrandController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole('admin', 'editor');
    }

    public function index(): string
    {
        return $this->view('admin/brands/index', [
            'brands' => (new Brand())->all('name ASC'),
        ], 'admin/layouts/app');
    }

    public function create(): string
    {
        if ($this->request->method() === 'POST') {
            return $this->persist(null);
        }
        return $this->view('admin/brands/form', ['brand' => null], 'admin/layouts/app');
    }

    public function edit(string $id): string
    {
        $brand = (new Brand())->find((int) $id);
        if (!$brand) {
            $this->abort(404);
        }
        if ($this->request->method() === 'POST') {
            return $this->persist($brand);
        }
        return $this->view('admin/brands/form', ['brand' => $brand], 'admin/layouts/app');
    }

    private function persist(?array $existing): string
    {
        $data = $this->request->all();
        $v = new Validator($data);
        if (!$v->validate(['name' => 'required|max:150'])) {
            Session::flash('error', $v->firstError());
            redirect($existing ? 'admin/brands/' . $existing['id'] . '/edit' : 'admin/brands/create');
        }
        $fields = [
            'name'        => trim((string) $data['name']),
            'slug'        => trim((string) ($data['slug'] ?? '')) ?: slugify((string) $data['name']),
            'description' => trim((string) ($data['description'] ?? '')),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
        ];
        if (!empty($_FILES['logo']['name'])) {
            if ($path = Upload::image($_FILES['logo'], 'brands')) {
                $fields['logo'] = $path;
            }
        }
        $model = new Brand();
        $existing ? $model->update((int) $existing['id'], $fields) : $model->create($fields);
        Session::flash('success', 'Brand saved.');
        redirect('admin/brands');
    }

    public function destroy(string $id): string
    {
        (new Brand())->delete((int) $id);
        Session::flash('success', 'Brand deleted.');
        redirect('admin/brands');
    }
}
