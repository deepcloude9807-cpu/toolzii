<?php
/** @var array $post @var array $related @var array $comments */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= url('blog') ?>">Blog</a></li>
        <li class="breadcrumb-item active"><?= e(str_excerpt($post['title'], 40)) ?></li>
    </ol></nav>

    <div class="row g-4">
        <article class="col-lg-8">
            <span class="text-brand small"><?= e($post['category_name'] ?? 'Guide') ?></span>
            <h1 class="h2"><?= e($post['title']) ?></h1>
            <div class="post-meta text-muted small mb-3">
                By <?= e($post['author_name'] ?? 'ToolzyNet') ?> · <?= date('M j, Y', strtotime($post['published_at'] ?? $post['created_at'])) ?> · <?= (int) $post['reading_minutes'] ?> min read
            </div>
            <?php if (!empty($post['featured_image'])): ?><img src="<?= uploaded($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="img-fluid rounded mb-4"><?php endif; ?>
            <div class="rich-text article-body"><?= $post['body'] ?></div>

            <?php if (!empty($post['tags'])): ?>
                <div class="tags mt-4"><?php foreach ($post['tags'] as $t): ?><span class="chip"><?= e($t) ?></span><?php endforeach; ?></div>
            <?php endif; ?>

            <section id="comments" class="mt-5">
                <h2 class="h5">Comments (<?= count($comments) ?>)</h2>
                <?php foreach ($comments as $cm): ?>
                    <div class="comment-item"><strong><?= e($cm['author_name']) ?></strong> <span class="text-muted xsmall"><?= date('M j, Y', strtotime($cm['created_at'])) ?></span><p class="small mb-0"><?= e($cm['body']) ?></p></div>
                <?php endforeach; ?>
                <form action="<?= url('blog/' . $post['slug'] . '/comment') ?>" method="post" class="mt-3">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-6"><input name="author_name" class="form-control" placeholder="Name" required></div>
                        <div class="col-md-6"><input name="author_email" type="email" class="form-control" placeholder="Email (optional)"></div>
                        <div class="col-12"><textarea name="body" class="form-control" rows="3" placeholder="Your comment" required></textarea></div>
                        <div class="col-12"><button class="btn btn-brand">Post Comment</button></div>
                    </div>
                </form>
            </section>
        </article>

        <aside class="col-lg-4">
            <div class="sticky-sidebar">
                <?php if ($related): ?>
                    <div class="sidebar-block"><h3 class="h6">Related Posts</h3>
                        <?php foreach ($related as $r): ?>
                            <a href="<?= url('blog/' . $r['slug']) ?>" class="related-item">
                                <img src="<?= uploaded($r['featured_image'], 'img/blog-placeholder.svg') ?>" alt="<?= e($r['title']) ?>" loading="lazy">
                                <span><?= e(str_excerpt($r['title'], 60)) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
