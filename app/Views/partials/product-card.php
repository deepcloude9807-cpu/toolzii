<?php
/** @var array $product */
$p = $product;
$img = $p['primary_image'] ?? ($p['image'] ?? null);
$discount = (int) ($p['discount'] ?? 0);
?>
<div class="col-6 col-md-4 col-lg-3">
    <article class="card product-card h-100">
        <a href="<?= url('product/' . $p['slug']) ?>" class="card-img-wrap">
            <?php if ($discount > 0): ?><span class="badge deal-badge"><?= $discount ?>% OFF</span><?php endif; ?>
            <?php if (!empty($p['is_new'])): ?><span class="badge new-badge">NEW</span><?php endif; ?>
            <?php if (!empty($p['is_bestseller'])): ?><span class="badge best-badge">Best Seller</span><?php endif; ?>
            <img src="<?= uploaded($img) ?>" class="card-img-top" alt="<?= e($p['name']) ?>" loading="lazy">
        </a>
        <div class="card-body d-flex flex-column">
            <?php if (!empty($p['brand_name'])): ?>
                <span class="small text-muted"><?= e($p['brand_name']) ?></span>
            <?php endif; ?>
            <h3 class="product-title h6">
                <a href="<?= url('product/' . $p['slug']) ?>"><?= e($p['name']) ?></a>
            </h3>
            <div class="rating small mb-2">
                <?php $r = (float) ($p['rating'] ?? 0); for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-<?= $i <= round($r) ? 'solid' : 'regular' ?> fa-star"></i>
                <?php endfor; ?>
                <span class="text-muted">(<?= (int) ($p['rating_count'] ?? 0) ?>)</span>
            </div>
            <div class="price-row mt-auto">
                <?php if (!empty($p['price'])): ?>
                    <span class="price"><?= money((float) $p['price']) ?></span>
                    <?php if (!empty($p['original_price']) && $p['original_price'] > $p['price']): ?>
                        <span class="price-old"><?= money((float) $p['original_price']) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <a href="<?= url('product/' . $p['slug']) ?>" class="btn btn-sm btn-brand w-100 mt-2">View Deal</a>
        </div>
    </article>
</div>
