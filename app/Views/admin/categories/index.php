<?php /** @var array $categories */ ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Categories</h1>
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-brand btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Category</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Name</th><th>Slug</th><th>Order</th><th>Active</th><th></th></tr></thead>
    <tbody>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td><i class="fa-solid <?= e($c['icon'] ?: 'fa-tag') ?> me-2 text-brand"></i><?= e($c['name']) ?></td>
                <td class="small text-muted"><?= e($c['slug']) ?></td>
                <td><?= (int) $c['sort_order'] ?></td>
                <td><?= $c['is_active'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                <td class="text-end text-nowrap">
                    <a href="<?= url('admin/categories/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" action="<?= url('admin/categories/' . $c['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete category?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$categories): ?><tr><td colspan="5" class="text-center text-muted py-4">No categories.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
