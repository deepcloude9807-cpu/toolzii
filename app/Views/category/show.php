<?php
use App\Core\View;
/** @var array $category @var array $result @var array $brands @var array $filters */
$filters = $filters ?? [];
?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li>
        <li class="breadcrumb-item active"><?= e($category['name']) ?></li>
    </ol></nav>

    <header class="category-head mb-4">
        <h1 class="h3"><?= e($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?><p class="text-muted"><?= e($category['description']) ?></p><?php endif; ?>
    </header>

    <div class="row g-4">
        <aside class="col-lg-3">
            <form class="filter-panel" id="filterForm" data-category="<?= (int) $category['id'] ?>" method="get">
                <h3 class="h6">Filter</h3>
                <div class="mb-3">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">All brands</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= (int) $b['id'] ?>" <?= ((string) ($filters['brand_id'] ?? '') === (string) $b['id']) ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min ₹" value="<?= e((string) ($filters['min_price'] ?? '')) ?>"></div>
                    <div class="col-6"><input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max ₹" value="<?= e((string) ($filters['max_price'] ?? '')) ?>"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Sort</label>
                    <select name="sort" class="form-select form-select-sm">
                        <?php foreach (['' => 'Newest', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low', 'rating' => 'Top Rated', 'popular' => 'Most Popular'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ((string) ($filters['sort'] ?? '') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-brand btn-sm w-100" type="submit">Apply Filters</button>
            </form>
        </aside>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small" id="resultCount"><?= (int) $result['total'] ?> products</span>
            </div>
            <div class="row g-3" id="productGrid">
                <?php if ($result['data']): foreach ($result['data'] as $p) { View::partial('partials/product-card', ['product' => $p]); } else: ?>
                    <div class="col-12"><div class="empty-state"><i class="fa-solid fa-box-open"></i><p>No products found in this category yet.</p></div></div>
                <?php endif; ?>
            </div>

            <?php View::partial('partials/pagination', ['result' => $result, 'base' => url('category/' . $category['slug'])]); ?>
        </div>
    </div>
</div>
