<?php
use App\Core\View;
/** @var array $product */
$p = $product;
$images = $p['images'] ?: [['path' => null, 'alt' => $p['name']]];
$partners = [
    'amazon'   => ['label' => 'Buy on Amazon', 'icon' => 'fa-amazon', 'class' => 'btn-amazon', 'link' => $p['amazon_link']],
    'flipkart' => ['label' => 'Buy on Flipkart', 'icon' => 'fa-bag-shopping', 'class' => 'btn-flipkart', 'link' => $p['flipkart_link']],
    'meesho'   => ['label' => 'Buy on Meesho', 'icon' => 'fa-store', 'class' => 'btn-meesho', 'link' => $p['meesho_link']],
];
?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li>
        <?php if (!empty($p['category_slug'])): ?>
            <li class="breadcrumb-item"><a href="<?= url('category/' . $p['category_slug']) ?>"><?= e($p['category_name']) ?></a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active"><?= e($p['name']) ?></li>
    </ol></nav>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="product-gallery">
                <img id="mainImage" src="<?= uploaded($images[0]['path']) ?>" alt="<?= e($p['name']) ?>" class="img-fluid rounded main-img">
                <?php if (count($images) > 1): ?>
                    <div class="thumbs d-flex gap-2 mt-2 flex-wrap">
                        <?php foreach ($images as $img): ?>
                            <img src="<?= uploaded($img['path']) ?>" class="thumb" alt="<?= e($img['alt'] ?: $p['name']) ?>" loading="lazy" onclick="document.getElementById('mainImage').src=this.src">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-7">
            <?php if (!empty($p['brand_name'])): ?><span class="text-muted small"><?= e($p['brand_name']) ?></span><?php endif; ?>
            <h1 class="h3"><?= e($p['name']) ?></h1>
            <div class="rating mb-2">
                <?php $r = (float) $p['rating']; for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-<?= $i <= round($r) ? 'solid' : 'regular' ?> fa-star text-warning"></i>
                <?php endfor; ?>
                <span class="text-muted small"><?= number_format($r, 1) ?> · <?= (int) $p['rating_count'] ?> ratings</span>
            </div>
            <p class="lead-sm"><?= e($p['short_description']) ?></p>

            <div class="price-box my-3">
                <?php if (!empty($p['price'])): ?>
                    <span class="price-lg"><?= money((float) $p['price']) ?></span>
                    <?php if (!empty($p['original_price']) && $p['original_price'] > $p['price']): ?>
                        <span class="price-old"><?= money((float) $p['original_price']) ?></span>
                        <span class="badge deal-badge"><?= (int) $p['discount'] ?>% OFF</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="buy-buttons d-grid gap-2">
                <?php foreach ($partners as $key => $partner): if (empty($partner['link'])) continue; ?>
                    <a href="<?= url('go/' . $p['id'] . '?partner=' . $key) ?>" target="_blank" rel="nofollow sponsored noopener" class="btn <?= $partner['class'] ?> btn-lg">
                        <i class="fa-brands <?= $partner['icon'] ?> me-2"></i><?= $partner['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($p['pros'] || $p['cons']): ?>
            <div class="row g-3 mt-3">
                <?php if ($p['pros']): ?>
                <div class="col-md-6"><div class="proscons pros">
                    <h4 class="h6"><i class="fa-solid fa-thumbs-up me-1"></i>Pros</h4>
                    <ul><?php foreach ($p['pros'] as $pro): ?><li><?= e($pro) ?></li><?php endforeach; ?></ul>
                </div></div>
                <?php endif; ?>
                <?php if ($p['cons']): ?>
                <div class="col-md-6"><div class="proscons cons">
                    <h4 class="h6"><i class="fa-solid fa-thumbs-down me-1"></i>Cons</h4>
                    <ul><?php foreach ($p['cons'] as $con): ?><li><?= e($con) ?></li><?php endforeach; ?></ul>
                </div></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Long description / features / specs -->
    <div class="row mt-5 g-4">
        <div class="col-lg-8">
            <?php if (!empty($p['long_description'])): ?>
                <section class="content-block"><h2 class="h5">Overview</h2><div class="rich-text"><?= $p['long_description'] ?></div></section>
            <?php endif; ?>

            <?php if ($p['features']): ?>
                <section class="content-block"><h2 class="h5">Key Features</h2>
                    <ul class="feature-list"><?php foreach ($p['features'] as $f): ?><li><i class="fa-solid fa-check text-brand me-2"></i><?= e($f) ?></li><?php endforeach; ?></ul>
                </section>
            <?php endif; ?>

            <?php if ($p['specifications']): ?>
                <section class="content-block"><h2 class="h5">Specifications</h2>
                    <table class="table spec-table"><tbody>
                        <?php foreach ($p['specifications'] as $k => $v): ?><tr><th><?= e((string) $k) ?></th><td><?= e((string) $v) ?></td></tr><?php endforeach; ?>
                    </tbody></table>
                </section>
            <?php endif; ?>

            <?php if ($p['faq']): ?>
                <section class="content-block"><h2 class="h5">Frequently Asked Questions</h2>
                    <div class="accordion" id="faqAccordion">
                        <?php foreach ($p['faq'] as $i => $f): ?>
                            <div class="accordion-item">
                                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"><?= e($f['q']) ?></button></h3>
                                <div id="faq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body"><?= e($f['a']) ?></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Reviews -->
            <section class="content-block" id="reviews">
                <h2 class="h5">Customer Reviews</h2>
                <?php if ($reviews): foreach ($reviews as $rev): ?>
                    <div class="review-item">
                        <div class="d-flex justify-content-between">
                            <strong><?= e($rev['author_name']) ?></strong>
                            <span class="text-warning small"><?php for ($i = 0; $i < (int) $rev['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?></span>
                        </div>
                        <?php if (!empty($rev['title'])): ?><div class="fw-semibold small"><?= e($rev['title']) ?></div><?php endif; ?>
                        <p class="small text-muted mb-1"><?= e($rev['body']) ?></p>
                        <span class="text-muted xsmall"><?= date('M j, Y', strtotime($rev['created_at'])) ?></span>
                    </div>
                <?php endforeach; else: ?>
                    <p class="text-muted">No reviews yet. Be the first to review this product.</p>
                <?php endif; ?>

                <form action="<?= url('product/' . $p['slug'] . '/review') ?>" method="post" class="review-form mt-3">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-6"><input name="author_name" class="form-control" placeholder="Your name" required></div>
                        <div class="col-md-6">
                            <select name="rating" class="form-select"><?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option><?php endfor; ?></select>
                        </div>
                        <div class="col-12"><input name="title" class="form-control" placeholder="Review title (optional)"></div>
                        <div class="col-12"><textarea name="body" class="form-control" rows="3" placeholder="Share your experience" required></textarea></div>
                        <div class="col-12"><button class="btn btn-brand">Submit Review</button></div>
                    </div>
                </form>
            </section>

            <div class="social-share mt-4">
                <span class="me-2">Share:</span>
                <a class="share-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('product/' . $p['slug'])) ?>"><i class="fa-brands fa-facebook-f"></i></a>
                <a class="share-btn" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?= urlencode(url('product/' . $p['slug'])) ?>&text=<?= urlencode($p['name']) ?>"><i class="fa-brands fa-x-twitter"></i></a>
                <a class="share-btn" target="_blank" rel="noopener" href="https://api.whatsapp.com/send?text=<?= urlencode($p['name'] . ' ' . url('product/' . $p['slug'])) ?>"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
        </div>

        <aside class="col-lg-4">
            <div class="sticky-sidebar">
                <?php if ($related): ?>
                    <div class="sidebar-block"><h3 class="h6">Related Products</h3>
                        <?php foreach ($related as $rp): ?>
                            <a href="<?= url('product/' . $rp['slug']) ?>" class="related-item">
                                <img src="<?= uploaded($rp['primary_image']) ?>" alt="<?= e($rp['name']) ?>" loading="lazy">
                                <span><?= e(str_excerpt($rp['name'], 48)) ?><br><small class="text-brand"><?= !empty($rp['price']) ? money((float) $rp['price']) : '' ?></small></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
