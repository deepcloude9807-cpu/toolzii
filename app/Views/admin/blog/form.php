<?php
/** @var array|null $post @var array $categories */
$b = $post;
$val = fn (string $k, string $d = '') => e((string) ($b[$k] ?? $d));
$tags = $b && !empty($b['tags']) ? implode(', ', json_decode((string) $b['tags'], true) ?: []) : '';
$action = $b ? url('admin/blog/' . $b['id'] . '/edit') : url('admin/blog/create');
?>
<h1 class="h4 mb-3"><?= $b ? 'Edit' : 'New' ?> Post</h1>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card card-body">
                <div class="mb-3"><label class="form-label">Title *</label><input name="title" class="form-control" value="<?= $val('title') ?>" required></div>
                <div class="mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="<?= $val('slug') ?>" placeholder="auto"></div>
                <div class="mb-3"><label class="form-label">Excerpt</label><textarea name="excerpt" class="form-control" rows="2"><?= $val('excerpt') ?></textarea></div>
                <div class="mb-3"><label class="form-label">Body (HTML) *</label><textarea name="body" class="form-control" rows="14"><?= $val('body') ?></textarea>
                    <small class="text-muted">A rich-text editor (TinyMCE/CKEditor) can be dropped in on this textarea.</small></div>
            </div>
            <div class="card card-body mt-3">
                <h2 class="h6">SEO</h2>
                <div class="mb-2"><label class="form-label small">Meta Title</label><input name="meta_title" class="form-control form-control-sm" value="<?= $val('meta_title') ?>"></div>
                <div class="mb-2"><label class="form-label small">Meta Description</label><textarea name="meta_description" class="form-control form-control-sm" rows="2"><?= $val('meta_description') ?></textarea></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-body mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select mb-3">
                    <?php foreach (['draft' => 'Draft', 'published' => 'Published', 'scheduled' => 'Scheduled'] as $k => $l): ?>
                        <option value="<?= $k ?>" <?= ($b['status'] ?? 'draft') === $k ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select mb-3"><option value="">— none —</option>
                    <?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($b['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
                </select>
                <label class="form-label">Tags (comma separated)</label>
                <input name="tags" class="form-control mb-3" value="<?= e($tags) ?>">
                <label class="form-label">Featured Image</label>
                <input type="file" name="featured_image" class="form-control mb-2" accept="image/*">
                <?php if (!empty($b['featured_image'])): ?><img src="<?= uploaded($b['featured_image']) ?>" class="img-fluid rounded" alt=""><?php endif; ?>
            </div>
            <button class="btn btn-brand w-100"><i class="fa-solid fa-floppy-disk me-1"></i>Save Post</button>
        </div>
    </div>
</form>
