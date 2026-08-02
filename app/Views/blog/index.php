<?php
/** @var array $result @var array $categories @var int|null $categoryId */
?>
<div class="container py-4">
    <header class="mb-4"><h1 class="h3">Blog</h1><p class="text-muted">Buying guides, tips and honest advice.</p></header>
    <div class="blog-filter mb-4">
        <a href="<?= url('blog') ?>" class="chip <?= !$categoryId ? 'active' : '' ?>">All</a>
        <?php foreach ($categories as $c): ?>
            <a href="<?= url('blog?category_id=' . $c['id']) ?>" class="chip <?= $categoryId === (int) $c['id'] ? 'active' : '' ?>"><?= e($c['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <div class="row g-4">
        <?php if ($result['data']): foreach ($result['data'] as $post): ?>
            <div class="col-md-6 col-lg-4">
                <article class="card blog-card h-100">
                    <a href="<?= url('blog/' . $post['slug']) ?>"><img src="<?= uploaded($post['featured_image'], 'img/blog-placeholder.svg') ?>" class="card-img-top" alt="<?= e($post['title']) ?>" loading="lazy"></a>
                    <div class="card-body">
                        <span class="small text-brand"><?= e($post['category_name'] ?? 'Guide') ?></span>
                        <h2 class="h6"><a href="<?= url('blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h2>
                        <p class="small text-muted"><?= e(str_excerpt($post['excerpt'] ?? '', 110)) ?></p>
                        <span class="xsmall text-muted"><i class="fa-regular fa-clock me-1"></i><?= (int) $post['reading_minutes'] ?> min read</span>
                    </div>
                </article>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12"><div class="empty-state"><i class="fa-solid fa-newspaper"></i><p>No posts published yet.</p></div></div>
        <?php endif; ?>
    </div>
    <?php \App\Core\View::partial('partials/pagination', ['result' => $result, 'base' => url('blog')]); ?>
</div>
