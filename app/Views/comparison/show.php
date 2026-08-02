<?php
/** @var array $c */
$a = $c['product_a'] ?? null;
$b = $c['product_b'] ?? null;
$winnerId = (int) ($c['winner_id'] ?? 0);
$buyRow = function (?array $p) {
    if (!$p) return '';
    $out = '<div class="d-grid gap-2">';
    if (!empty($p['amazon_link'])) $out .= '<a href="' . url('go/' . $p['id'] . '?partner=amazon') . '" target="_blank" rel="nofollow sponsored noopener" class="btn btn-amazon btn-sm"><i class="fa-brands fa-amazon me-1"></i>Amazon</a>';
    if (!empty($p['flipkart_link'])) $out .= '<a href="' . url('go/' . $p['id'] . '?partner=flipkart') . '" target="_blank" rel="nofollow sponsored noopener" class="btn btn-flipkart btn-sm">Flipkart</a>';
    return $out . '</div>';
};
?>
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= url('compare') ?>">Compare</a></li>
        <li class="breadcrumb-item active"><?= e($c['title']) ?></li>
    </ol></nav>

    <h1 class="h3 text-center mb-2"><?= e($c['title']) ?></h1>
    <?php if (!empty($c['intro'])): ?><p class="text-muted text-center mx-auto" style="max-width:720px"><?= e($c['intro']) ?></p><?php endif; ?>

    <div class="row g-3 my-4 justify-content-center">
        <?php foreach ([$a, $b] as $p): if (!$p) continue; ?>
            <div class="col-md-5">
                <div class="vs-card <?= $winnerId === (int) $p['id'] ? 'winner' : '' ?>">
                    <?php if ($winnerId === (int) $p['id']): ?><span class="winner-badge"><i class="fa-solid fa-crown me-1"></i>Winner</span><?php endif; ?>
                    <img src="<?= uploaded($p['image'] ?? null) ?>" alt="<?= e($p['name']) ?>" class="img-fluid mb-2">
                    <h2 class="h6"><?= e($p['name']) ?></h2>
                    <?php if (!empty($p['price'])): ?><div class="price mb-2"><?= money((float) $p['price']) ?></div><?php endif; ?>
                    <?= $buyRow($p) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($c['comparison_table'])): ?>
        <div class="table-responsive">
            <table class="table comparison-table align-middle">
                <thead><tr><th>Feature</th><th><?= e($a['name'] ?? 'A') ?></th><th><?= e($b['name'] ?? 'B') ?></th></tr></thead>
                <tbody>
                    <?php foreach ($c['comparison_table'] as $row): ?>
                        <tr><th><?= e($row['feature'] ?? '') ?></th><td><?= e($row['a'] ?? '') ?></td><td><?= e($row['b'] ?? '') ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (!empty($c['verdict'])): ?>
        <div class="verdict-box mt-4"><h2 class="h5"><i class="fa-solid fa-gavel me-2"></i>Our Verdict</h2><p class="mb-0"><?= nl2br(e($c['verdict'])) ?></p></div>
    <?php endif; ?>
</div>
