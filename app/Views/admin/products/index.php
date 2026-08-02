<?php /** @var array $result @var string $q */ ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Products</h1>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/products/import') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-file-csv me-1"></i>Import CSV</a>
        <a href="<?= url('admin/products/create') ?>" class="btn btn-brand btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Product</a>
    </div>
</div>

<form class="mb-3" method="get"><div class="input-group input-group-sm" style="max-width:320px">
    <input name="q" class="form-control" placeholder="Search products…" value="<?= e($q) ?>"><button class="btn btn-outline-secondary">Search</button>
</div></form>

<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Status</th><th>Flags</th><th>Views</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($result['data'] as $p): ?>
            <tr>
                <td><img src="<?= uploaded($p['primary_image']) ?>" class="admin-thumb" alt=""></td>
                <td><strong><?= e($p['name']) ?></strong><br><small class="text-muted"><?= e($p['slug']) ?></small></td>
                <td><?= !empty($p['price']) ? money((float) $p['price']) : '—' ?></td>
                <td><span class="badge bg-<?= $p['status'] === 'published' ? 'success' : ($p['status'] === 'scheduled' ? 'info' : 'secondary') ?>"><?= e($p['status']) ?></span></td>
                <td class="small">
                    <?= $p['is_featured'] ? '<span class="badge bg-warning">Feat</span> ' : '' ?>
                    <?= $p['is_trending'] ? '<span class="badge bg-danger">Trend</span> ' : '' ?>
                    <?= $p['is_deal'] ? '<span class="badge bg-primary">Deal</span>' : '' ?>
                </td>
                <td><?= (int) $p['views'] ?></td>
                <td class="text-end text-nowrap">
                    <a href="<?= url('admin/products/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" action="<?= url('admin/products/' . $p['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this product?')">
                        <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$result['data']): ?><tr><td colspan="7" class="text-center text-muted py-4">No products yet. <a href="<?= url('admin/products/create') ?>">Add your first product</a>.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
<?php \App\Core\View::partial('partials/pagination', ['result' => $result, 'base' => url('admin/products')]); ?>
