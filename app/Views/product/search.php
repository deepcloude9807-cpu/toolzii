<?php
use App\Core\View;
/** @var string $term @var array $result @var array $categories @var array $brands @var array $filters */
$filters = $filters ?? [];
?>
<div class="container py-4">
    <h1 class="h4 mb-3"><?= $term !== '' ? 'Results for "' . e($term) . '"' : 'Search Products' ?></h1>

    <div class="row g-4">
        <aside class="col-lg-3">
            <form class="filter-panel" method="get" action="<?= url('search') ?>">
                <input type="hidden" name="q" value="<?= e($term) ?>">
                <h3 class="h6">Refine</h3>
                <div class="mb-3">
                    <label class="form-label small">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= ((string) ($filters['category_id'] ?? '') === (string) $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">All</option>
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
                    <select name="sort" class="form-select form-select-sm">
                        <?php foreach (['' => 'Relevance', 'price_low' => 'Price ↑', 'price_high' => 'Price ↓', 'rating' => 'Top Rated', 'popular' => 'Popular'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ((string) ($filters['sort'] ?? '') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-brand btn-sm w-100">Apply</button>
            </form>
        </aside>
        <div class="col-lg-9">
            <span class="text-muted small"><?= (int) $result['total'] ?> products</span>
            <div class="row g-3 mt-1">
                <?php if ($result['data']): foreach ($result['data'] as $p) { View::partial('partials/product-card', ['product' => $p]); } else: ?>
                    <div class="col-12"><div class="empty-state"><i class="fa-solid fa-magnifying-glass"></i><p>No products matched your search.</p></div></div>
                <?php endif; ?>
            </div>
            <?php View::partial('partials/pagination', ['result' => $result, 'base' => url('search')]); ?>
        </div>
    </div>
</div>
