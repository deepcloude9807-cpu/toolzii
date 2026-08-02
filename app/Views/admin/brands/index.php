<?php /** @var array $brands */ ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Brands</h1>
    <a href="<?= url('admin/brands/create') ?>" class="btn btn-brand btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Brand</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Logo</th><th>Name</th><th>Slug</th><th>Active</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($brands as $b): ?>
            <tr>
                <td><?php if (!empty($b['logo'])): ?><img src="<?= uploaded($b['logo']) ?>" class="admin-thumb" alt=""><?php else: ?>—<?php endif; ?></td>
                <td><?= e($b['name']) ?></td><td class="small text-muted"><?= e($b['slug']) ?></td>
                <td><?= $b['is_active'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                <td class="text-end text-nowrap">
                    <a href="<?= url('admin/brands/' . $b['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" action="<?= url('admin/brands/' . $b['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete brand?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$brands): ?><tr><td colspan="5" class="text-center text-muted py-4">No brands.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
