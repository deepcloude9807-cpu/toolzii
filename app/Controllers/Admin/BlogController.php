<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Upload;
use App\Core\Database;
use App\Models\Blog;
use App\Models\Category;

final class BlogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole('admin', 'editor', 'author');
    }

    public function index(): string
    {
        $page = max(1, (int) $this->request->input('page', 1));
        $result = (new Blog())->paginate($page, 15, '1=1', [], 'id DESC');
        return $this->view('admin/blog/index', ['result' => $result], 'admin/layouts/app');
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
        $post = (new Blog())->find((int) $id);
        if (!$post) {
            $this->abort(404);
        }
        if ($this->request->method() === 'POST') {
            return $this->persist($post);
        }
        return $this->form($post);
    }

    private function form(?array $post): string
    {
        return $this->view('admin/blog/form', [
            'post'       => $post,
            'categories' => (new Category())->all('name ASC'),
        ], 'admin/layouts/app');
    }

    private function persist(?array $existing): string
    {
        $data = $this->request->all();
        $v = new Validator($data);
        if (!$v->validate(['title' => 'required|max:220', 'body' => 'required|min:20'])) {
            Session::flash('error', $v->firstError());
            redirect($existing ? 'admin/blog/' . $existing['id'] . '/edit' : 'admin/blog/create');
        }

        $slug = trim((string) ($data['slug'] ?? '')) ?: slugify((string) $data['title']);
        $slug = $this->uniqueSlug($slug, $existing['id'] ?? null);
        $tags = array_values(array_filter(array_map('trim', explode(',', (string) ($data['tags'] ?? '')))));
        $wordCount = str_word_count(strip_tags((string) $data['body']));

        $fields = [
            'category_id'     => $data['category_id'] ?: null,
            'author_id'       => Auth::id(),
            'title'           => trim((string) $data['title']),
            'slug'            => $slug,
            'excerpt'         => trim((string) ($data['excerpt'] ?? '')) ?: str_excerpt((string) $data['body'], 200),
            'body'            => (string) $data['body'],
            'tags'            => $tags ? json_encode($tags) : null,
            'reading_minutes' => max(1, (int) ceil($wordCount / 200)),
            'status'          => in_array($data['status'] ?? 'draft', ['draft', 'published', 'scheduled'], true) ? $data['status'] : 'draft',
            'published_at'    => ($data['status'] ?? '') === 'published' ? date('Y-m-d H:i:s') : ($data['published_at'] ?: null),
            'meta_title'      => trim((string) ($data['meta_title'] ?? '')) ?: null,
            'meta_description' => trim((string) ($data['meta_description'] ?? '')) ?: null,
        ];
        if (!empty($_FILES['featured_image']['name'])) {
            if ($path = Upload::image($_FILES['featured_image'], 'blog')) {
                $fields['featured_image'] = $path;
            }
        }

        $model = new Blog();
        $existing ? $model->update((int) $existing['id'], $fields) : $model->create($fields);
        Session::flash('success', 'Post saved.');
        redirect('admin/blog');
    }

    public function destroy(string $id): string
    {
        (new Blog())->delete((int) $id);
        Session::flash('success', 'Post moved to trash.');
        redirect('admin/blog');
    }

    private function uniqueSlug(string $slug, ?int $ignoreId): string
    {
        $base = $slug;
        $i = 1;
        while (Database::value('SELECT id FROM blog_posts WHERE slug = ?' . ($ignoreId ? ' AND id <> ?' : ''), $ignoreId ? [$slug, $ignoreId] : [$slug])) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}
