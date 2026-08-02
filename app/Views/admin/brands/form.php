<?php
/** @var array|null $brand */
$b = $brand;
$val = fn (string $k, string $d = '') => e((string) ($b[$k] ?? $d));
$action = $b ? url('admin/brands/' . $b['id'] . '/edit') : url('admin/brands/create');
?>
<h1 class="h4 mb-3"><?= $b ? 'Edit' : 'Add' ?> Brand</h1>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data" style="max-width:640px">
    <?= csrf_field() ?>
    <div class="card card-body">
        <div class="mb-3"><label class="form-label">Name *</label><input name="name" class="form-control" value="<?= $val('name') ?>" required></div>
        <div class="mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="<?= $val('slug') ?>" placeholder="auto"></div>
        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= $val('description') ?></textarea></div>
        <div class="mb-3"><label class="form-label">Logo</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_active" id="ba" <?= (!$b || $b['is_active']) ? 'checked' : '' ?>><label class="form-check-label" for="ba">Active</label></div>
        <button class="btn btn-brand">Save</button> <a href="<?= url('admin/brands') ?>" class="btn btn-link">Cancel</a>
    </div>
</form>
