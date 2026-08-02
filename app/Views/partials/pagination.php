<?php
/** @var array $result @var string $base */
$page = (int) ($result['page'] ?? 1);
$last = (int) ($result['last_page'] ?? 1);
if ($last <= 1) return;
$q = $_GET;
$link = function (int $p) use ($base, $q) {
    $q['page'] = $p;
    return $base . '?' . http_build_query($q);
};
$start = max(1, $page - 2);
$end = min($last, $page + 2);
?>
<nav aria-label="Pagination" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $page > 1 ? e($link($page - 1)) : '#' ?>">&laquo;</a></li>
        <?php if ($start > 1): ?><li class="page-item"><a class="page-link" href="<?= e($link(1)) ?>">1</a></li><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= e($link($i)) ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <?php if ($end < $last): ?><li class="page-item disabled"><span class="page-link">…</span></li><li class="page-item"><a class="page-link" href="<?= e($link($last)) ?>"><?= $last ?></a></li><?php endif; ?>
        <li class="page-item <?= $page >= $last ? 'disabled' : '' ?>"><a class="page-link" href="<?= $page < $last ? e($link($page + 1)) : '#' ?>">&raquo;</a></li>
    </ul>
</nav>
