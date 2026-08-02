<?php
use App\Core\View;
use App\Models\Product;
$pm = new Product();
$attach = fn (array $p) => array_merge($p, ['primary_image' => $pm->primaryImage((int) $p['id'])]);
?>
<!-- Hero -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="hero-eyebrow">Smart shopping starts here</span>
                <h1 class="hero-title">Compare products, read honest reviews &amp; grab the <span class="text-brand">best deals</span>.</h1>
                <p class="hero-sub">Hand-picked recommendations across mobiles, laptops, gadgets and more — with real pros, cons and prices from Amazon, Flipkart &amp; Meesho.</p>
                <form action="<?= url('search') ?>" method="get" class="hero-search">
                    <input type="search" name="q" class="form-control form-control-lg" placeholder="What are you looking for?" aria-label="Search products">
                    <button class="btn btn-brand btn-lg" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
                </form>
                <div class="hero-tags">
                    <?php foreach (array_slice($categories, 0, 5) as $c): ?>
                        <a href="<?= url('category/' . $c['slug']) ?>" class="chip"><?= e($c['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-card glass">
                    <div class="d-flex justify-content-between mb-3"><span class="fw-semibold">Today's Top Pick</span><span class="badge deal-badge">Hot</span></div>
                    <?php $pick = $trending[0] ?? ($featured[0] ?? null); if ($pick): $pick = $attach($pick); ?>
                        <img src="<?= uploaded($pick['primary_image']) ?>" alt="<?= e($pick['name']) ?>" class="img-fluid rounded mb-3">
                        <h3 class="h6"><?= e($pick['name']) ?></h3>
                        <a href="<?= url('product/' . $pick['slug']) ?>" class="btn btn-brand btn-sm w-100">View Review</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="container py-5">
    <div class="section-head"><h2>Top Categories</h2><a href="<?= url('search') ?>" class="link-more">Browse all</a></div>
    <div class="category-grid">
        <?php foreach (array_slice($categories, 0, 12) as $c): ?>
            <a href="<?= url('category/' . $c['slug']) ?>" class="category-tile">
                <span class="cat-icon"><i class="fa-solid <?= e($c['icon'] ?: 'fa-tag') ?>"></i></span>
                <span class="cat-name"><?= e($c['name']) ?></span>
                <span class="cat-count"><?= (int) ($c['product_count'] ?? 0) ?> items</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Trending -->
<?php if ($trending): ?>
<section class="container py-4">
    <div class="section-head"><h2><i class="fa-solid fa-fire text-danger me-2"></i>Trending Products</h2></div>
    <div class="row g-3">
        <?php foreach ($trending as $p) { View::partial('partials/product-card', ['product' => $attach($p)]); } ?>
    </div>
</section>
<?php endif; ?>

<!-- Today's Deals -->
<?php if ($deals): ?>
<section class="deals-band py-5 my-4">
    <div class="container">
        <div class="section-head"><h2><i class="fa-solid fa-bolt text-warning me-2"></i>Today's Deals</h2></div>
        <div class="row g-3">
            <?php foreach ($deals as $p) { View::partial('partials/product-card', ['product' => $attach($p)]); } ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured -->
<?php if ($featured): ?>
<section class="container py-4">
    <div class="section-head"><h2><i class="fa-solid fa-star text-warning me-2"></i>Featured Reviews</h2></div>
    <div class="row g-3">
        <?php foreach ($featured as $p) { View::partial('partials/product-card', ['product' => $attach($p)]); } ?>
    </div>
</section>
<?php endif; ?>

<!-- Comparisons -->
<?php if ($comparisons): ?>
<section class="container py-4">
    <div class="section-head"><h2>Best Comparison Articles</h2><a href="<?= url('compare') ?>" class="link-more">See all</a></div>
    <div class="row g-3">
        <?php foreach ($comparisons as $c): ?>
            <div class="col-md-6 col-lg-3">
                <a href="<?= url('compare/' . $c['slug']) ?>" class="compare-card">
                    <span class="badge bg-brand-soft mb-2">VS</span>
                    <h3 class="h6"><?= e($c['title']) ?></h3>
                    <p class="small text-muted mb-0"><?= e(str_excerpt($c['intro'] ?? '', 80)) ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Latest Blog -->
<?php if ($latestPosts): ?>
<section class="container py-4">
    <div class="section-head"><h2>Latest From The Blog</h2><a href="<?= url('blog') ?>" class="link-more">All posts</a></div>
    <div class="row g-4">
        <?php foreach ($latestPosts as $post): ?>
            <div class="col-md-4">
                <article class="card blog-card h-100">
                    <a href="<?= url('blog/' . $post['slug']) ?>">
                        <img src="<?= uploaded($post['featured_image'], 'img/blog-placeholder.svg') ?>" class="card-img-top" alt="<?= e($post['title']) ?>" loading="lazy">
                    </a>
                    <div class="card-body">
                        <span class="small text-brand"><?= e($post['category_name'] ?? 'Guide') ?></span>
                        <h3 class="h6"><a href="<?= url('blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h3>
                        <p class="small text-muted mb-0"><?= e(str_excerpt($post['excerpt'] ?? '', 100)) ?></p>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter band -->
<section class="newsletter-band my-5">
    <div class="container text-center">
        <h2 class="mb-2">Never miss a deal</h2>
        <p class="text-muted mb-3">Join our newsletter for weekly hand-picked offers.</p>
        <form id="newsletterFormBig" class="newsletter-form mx-auto" style="max-width:460px">
            <div class="input-group input-group-lg">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                <button class="btn btn-brand" type="submit">Subscribe</button>
            </div>
            <small class="form-text" id="newsletterMsgBig"></small>
        </form>
    </div>
</section>
