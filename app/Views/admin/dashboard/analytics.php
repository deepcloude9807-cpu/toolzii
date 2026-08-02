<?php /** @var array $byPartner @var array $clicksSeries @var array $topClicked @var array $topSearches @var array $mostViewed */ ?>
<h1 class="h4 mb-4">Analytics</h1>
<div class="row g-3">
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h2 class="h6">Clicks by Partner</h2>
        <table class="table table-sm mb-0"><tbody>
            <?php foreach ($byPartner as $r): ?><tr><td class="text-capitalize"><?= e($r['partner']) ?></td><td class="text-end"><?= (int) $r['c'] ?></td></tr><?php endforeach; ?>
            <?php if (!$byPartner): ?><tr><td class="text-muted small">No data.</td></tr><?php endif; ?>
        </tbody></table>
    </div></div></div>

    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h2 class="h6">Top Searches</h2>
        <table class="table table-sm mb-0"><tbody>
            <?php foreach ($topSearches as $s): ?><tr><td><?= e($s['term']) ?></td><td class="text-end"><?= (int) $s['c'] ?></td></tr><?php endforeach; ?>
            <?php if (!$topSearches): ?><tr><td class="text-muted small">No searches yet.</td></tr><?php endif; ?>
        </tbody></table>
    </div></div></div>

    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h2 class="h6">Top Clicked Products</h2>
        <table class="table table-sm mb-0"><tbody>
            <?php foreach ($topClicked as $t): ?><tr><td><?= e(str_excerpt($t['name'], 40)) ?></td><td class="text-end"><?= (int) $t['clicks'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div></div></div>

    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h2 class="h6">Most Viewed Products</h2>
        <table class="table table-sm mb-0"><tbody>
            <?php foreach ($mostViewed as $m): ?><tr><td><?= e(str_excerpt($m['name'], 40)) ?></td><td class="text-end"><?= (int) $m['views'] ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </div></div></div>
</div>
