<?php
/** @var array $stats @var array $topProducts @var array $topClicked @var array $recentReviews @var array $clicksSeries */
$cards = [
    ['label' => 'Products', 'value' => $stats['products'], 'icon' => 'fa-box', 'color' => 'primary'],
    ['label' => 'Categories', 'value' => $stats['categories'], 'icon' => 'fa-layer-group', 'color' => 'info'],
    ['label' => 'Blog Posts', 'value' => $stats['posts'], 'icon' => 'fa-newspaper', 'color' => 'success'],
    ['label' => 'Affiliate Clicks', 'value' => $stats['clicks'], 'icon' => 'fa-arrow-pointer', 'color' => 'warning'],
    ['label' => 'Subscribers', 'value' => $stats['subscribers'], 'icon' => 'fa-envelope', 'color' => 'secondary'],
    ['label' => 'Unread Messages', 'value' => $stats['messages'], 'icon' => 'fa-comment-dots', 'color' => 'danger'],
];
$maxClicks = max(1, ...array_map(fn ($r) => (int) $r['c'], $clicksSeries ?: [['c' => 0]]));
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Dashboard</h1>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-brand btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Product</a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($cards as $c): ?>
        <div class="col-6 col-lg-2">
            <div class="stat-card border-start-<?= $c['color'] ?>">
                <div class="stat-icon text-<?= $c['color'] ?>"><i class="fa-solid <?= $c['icon'] ?>"></i></div>
                <div class="stat-value"><?= number_format((int) $c['value']) ?></div>
                <div class="stat-label"><?= $c['label'] ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <h2 class="h6">Affiliate Clicks (last 30 days)</h2>
            <div class="mini-chart">
                <?php foreach ($clicksSeries as $row): $h = round(((int) $row['c'] / $maxClicks) * 100); ?>
                    <div class="bar" style="height:<?= max(4, $h) ?>%" title="<?= e($row['d']) ?>: <?= (int) $row['c'] ?>"></div>
                <?php endforeach; ?>
                <?php if (!$clicksSeries): ?><p class="text-muted small mb-0">No click data yet.</p><?php endif; ?>
            </div>
        </div></div>

        <div class="card mt-3"><div class="card-body">
            <h2 class="h6">Top Clicked Products</h2>
            <table class="table table-sm mb-0"><tbody>
                <?php foreach ($topClicked as $t): ?>
                    <tr><td><a href="<?= url('product/' . $t['slug']) ?>" target="_blank"><?= e($t['name']) ?></a></td><td class="text-end"><span class="badge bg-warning"><?= (int) $t['clicks'] ?> clicks</span></td></tr>
                <?php endforeach; ?>
                <?php if (!$topClicked): ?><tr><td class="text-muted small">No clicks recorded yet.</td></tr><?php endif; ?>
            </tbody></table>
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h2 class="h6">Most Viewed</h2>
            <ul class="list-unstyled mb-0 small">
                <?php foreach ($topProducts as $p): ?><li class="d-flex justify-content-between py-1 border-bottom"><a href="<?= url('product/' . $p['slug']) ?>" target="_blank"><?= e(str_excerpt($p['name'], 30)) ?></a><span class="text-muted"><?= (int) $p['views'] ?> views</span></li><?php endforeach; ?>
                <?php if (!$topProducts): ?><li class="text-muted">No data.</li><?php endif; ?>
            </ul>
        </div></div>

        <div class="card mt-3"><div class="card-body">
            <h2 class="h6">Recent Reviews</h2>
            <?php foreach ($recentReviews as $r): ?>
                <div class="small border-bottom py-1"><strong><?= e($r['author_name']) ?></strong> <span class="text-warning">★<?= (int) $r['rating'] ?></span><br><span class="text-muted"><?= e(str_excerpt($r['product'], 30)) ?></span></div>
            <?php endforeach; ?>
            <?php if (!$recentReviews): ?><p class="text-muted small mb-0">No reviews yet.</p><?php endif; ?>
        </div></div>
    </div>
</div>
