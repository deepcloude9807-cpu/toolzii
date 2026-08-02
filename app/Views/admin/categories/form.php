<?php
/** @var array|null $category @var array $parents */
$c = $category;
$val = fn (string $k, string $d = '') => e((string) ($c[$k] ?? $d));
$action = $c ? url('admin/categories/' . $c['id'] . '/edit') : url('admin/categories/create');
?>
<h1 class="h4 mb-3"><?= $c ? 'Edit' : 'Add' ?> Category</h1>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data" style="max-width:720px">
    <?= csrf_field() ?>
    <div class="card card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name *</label><input name="name" class="form-control" value="<?= $val('name') ?>" required></div>
            <div class="col-md-6"><label class="form-label">Slug</label><input name="slug" class="form-control" value="<?= $val('slug') ?>" placeholder="auto"></div>
            <div class="col-md-6"><label class="form-label">Parent Category</label>
                <select name="parent_id" class="form-select"><option value="">— none —</option>
                    <?php foreach ($parents as $pc): if ($c && (int) $pc['id'] === (int) $c['id']) continue; ?>
                        <option value="<?= (int) $pc['id'] ?>" <?= (int) ($c['parent_id'] ?? 0) === (int) $pc['id'] ? 'selected' : '' ?>><?= e($pc['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Icon (FA class)</label><input name="icon" class="form-control" value="<?= $val('icon') ?>" placeholder="fa-mobile"></div>
            <div class="col-md-3"><label class="form-label">Sort Order</label><input name="sort_order" type="number" class="form-control" value="<?= $val('sort_order', '0') ?>"></div>
            <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= $val('description') ?></textarea></div>
            <div class="col-12"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="col-md-6"><label class="form-label">Meta Title</label><input name="meta_title" class="form-control" value="<?= $val('meta_title') ?>"></div>
            <div class="col-md-6"><label class="form-label">Meta Description</label><input name="meta_description" class="form-control" value="<?= $val('meta_description') ?>"></div>
            <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" id="ca" <?= (!$c || $c['is_active']) ? 'checked' : '' ?>><label class="form-check-label" for="ca">Active</label></div></div>
        </div>
        <div class="mt-3"><button class="btn btn-brand">Save</button> <a href="<?= url('admin/categories') ?>" class="btn btn-link">Cancel</a></div>
    </div>
</form>
