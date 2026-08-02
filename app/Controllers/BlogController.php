<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Seo;
use App\Core\Session;
use App\Core\Validator;
use App\Core\RateLimiter;
use App\Core\Database;
use App\Models\Blog;
use App\Models\Category;

final class BlogController extends Controller
{
    public function index(): string
    {
        $page = max(1, (int) $this->request->input('page', 1));
        $categoryId = (int) $this->request->input('category_id', 0) ?: null;
        $result = (new Blog())->published($page, config('app.blog_per_page'), $categoryId);

        $this->seo([
            'title'       => 'Blog - Guides, Tips & Buying Advice | ' . config('app.name'),
            'description' => 'Read the latest buying guides, product tips and tech advice on the ToolzyNet blog.',
            'canonical'   => url('blog'),
        ]);

        return $this->view('blog/index', [
            'result'     => $result,
            'categories' => (new Category())->menu(50),
            'categoryId' => $categoryId,
        ]);
    }

    public function show(string $slug): string
    {
        $model = new Blog();
        $post = $model->detailBySlug($slug);
        if (!$post) {
            $this->abort(404, 'Article not found.');
        }
        $model->incrementViews((int) $post['id']);

        $schemas = [
            Seo::jsonLd(Seo::article($post)),
            Seo::jsonLd(Seo::breadcrumb([
                'Home' => url(''),
                'Blog' => url('blog'),
                $post['title'] => url('blog/' . $post['slug']),
            ])),
        ];

        $this->seo([
            'title'       => $post['meta_title'] ?: ($post['title'] . ' | ' . config('app.name')),
            'description' => $post['meta_description'] ?: str_excerpt($post['excerpt'] ?? $post['body'] ?? '', 160),
            'canonical'   => url('blog/' . $post['slug']),
            'og_image'    => uploaded($post['og_image'] ?: $post['featured_image']),
            'type'        => 'article',
            'schema'      => implode("\n", $schemas),
        ]);

        return $this->view('blog/show', [
            'post'     => $post,
            'related'  => $model->related((int) ($post['category_id'] ?? 0), (int) $post['id'], 3),
            'comments' => $model->approvedComments((int) $post['id']),
        ]);
    }

    public function comment(string $slug): string
    {
        $post = (new Blog())->findBy('slug', $slug);
        if (!$post) {
            $this->abort(404);
        }
        if (RateLimiter::tooMany('comment:' . $this->request->ip(), 5, 3600)) {
            Session::flash('error', 'Too many comments. Please slow down.');
            redirect('blog/' . $slug);
        }
        $v = new Validator($this->request->all());
        if (!$v->validate([
            'author_name' => 'required|max:120',
            'author_email' => 'email',
            'body'        => 'required|min:3|max:2000',
        ])) {
            Session::flash('error', $v->firstError());
            redirect('blog/' . $slug . '#comments');
        }
        Database::run(
            'INSERT INTO blog_comments (post_id, author_name, author_email, body, is_approved) VALUES (?, ?, ?, ?, 0)',
            [
                $post['id'],
                trim((string) $this->request->input('author_name')),
                trim((string) $this->request->input('author_email', '')),
                trim((string) $this->request->input('body')),
            ]
        );
        Session::flash('success', 'Comment submitted for moderation.');
        redirect('blog/' . $slug . '#comments');
    }
}
