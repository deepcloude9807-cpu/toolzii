<?php /** @var array $comparisons */ ?>
<div class="container py-4">
    <header class="mb-4"><h1 class="h3">Product Comparisons</h1><p class="text-muted">Side-by-side breakdowns to help you pick the winner.</p></header>
    <div class="row g-3">
        <?php if ($comparisons): foreach ($comparisons as $c): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= url('compare/' . $c['slug']) ?>" class="compare-card h-100">
                    <span class="badge bg-brand-soft mb-2">VS</span>
                    <h2 class="h6"><?= e($c['title']) ?></h2>
                    <p class="small text-muted mb-0"><?= e(str_excerpt($c['intro'] ?? '', 110)) ?></p>
                </a>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12"><div class="empty-state"><i class="fa-solid fa-scale-balanced"></i><p>No comparisons published yet.</p></div></div>
        <?php endif; ?>
    </div>
</div>
