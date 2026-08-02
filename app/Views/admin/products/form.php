<?php
use App\Core\Auth;
/** @var array|null $product @var array $categories @var array $brands @var array $images */
$p = $product;
$val = fn (string $k, string $d = '') => e((string) ($p[$k] ?? $d));
$json = function (string $k) use ($p) {
    $raw = $p[$k] ?? null;
    if (!$raw) return '';
    $arr = json_decode((string) $raw, true) ?: [];
    return e(implode("\n", $arr));
};
$specs = $p && !empty($p['specifications']) ? (json_decode((string) $p['specifications'], true) ?: []) : [];
$faqs = $p && !empty($p['faq']) ? (json_decode((string) $p['faq'], true) ?: []) : [];
$action = $p ? url('admin/products/' . $p['id'] . '/edit') : url('admin/products/create');
?>
<h1 class="h4 mb-3"><?= $p ? 'Edit' : 'Add' ?> Product</h1>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card card-body mb-3">
                <div class="mb-3"><label class="form-label">Name *</label><input name="name" class="form-control" value="<?= $val('name', old('name')) ?>" required></div>
                <div class="row g-2">
                    <div class="col-md-6 mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="<?= $val('slug') ?>" placeholder="auto-generated"></div>
                    <div class="col-md-3 mb-3"><label class="form-label">Rating</label><input name="rating" type="number" step="0.1" min="0" max="5" class="form-control" value="<?= $val('rating', '0') ?>"></div>
                </div>
                <div class="mb-3"><label class="form-label">Short Description</label><textarea name="short_description" class="form-control" rows="2"><?= $val('short_description') ?></textarea></div>
                <div class="mb-3"><label class="form-label">Long Description (HTML allowed)</label><textarea name="long_description" class="form-control" rows="6"><?= $val('long_description') ?></textarea></div>
            </div>

            <div class="card card-body mb-3">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Features (one per line)</label><textarea name="features" class="form-control" rows="5"><?= $json('features') ?></textarea></div>
                    <div class="col-md-3"><label class="form-label">Pros (one per line)</label><textarea name="pros" class="form-control" rows="5"><?= $json('pros') ?></textarea></div>
                    <div class="col-md-3"><label class="form-label">Cons (one per line)</label><textarea name="cons" class="form-control" rows="5"><?= $json('cons') ?></textarea></div>
                </div>
            </div>

            <div class="card card-body mb-3">
                <label class="form-label">Specifications</label>
                <div id="specRows">
                    <?php $specs = $specs ?: ['' => '']; foreach ($specs as $k => $v): ?>
                        <div class="row g-2 mb-2 spec-row"><div class="col-5"><input name="spec_key[]" class="form-control form-control-sm" placeholder="Label" value="<?= e((string) $k) ?>"></div><div class="col-6"><input name="spec_val[]" class="form-control form-control-sm" placeholder="Value" value="<?= e((string) $v) ?>"></div><div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></div></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-add="spec">+ Add spec</button>
            </div>

            <div class="card card-body mb-3">
                <label class="form-label">FAQ</label>
                <div id="faqRows">
                    <?php $faqs = $faqs ?: [['q' => '', 'a' => '']]; foreach ($faqs as $f): ?>
                        <div class="row g-2 mb-2 faq-row"><div class="col-5"><input name="faq_q[]" class="form-control form-control-sm" placeholder="Question" value="<?= e($f['q'] ?? '') ?>"></div><div class="col-6"><input name="faq_a[]" class="form-control form-control-sm" placeholder="Answer" value="<?= e($f['a'] ?? '') ?>"></div><div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></div></div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-add="faq">+ Add FAQ</button>
            </div>

            <div class="card card-body mb-3">
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
                        <option value="<?= $k ?>" <?= ($p['status'] ?? 'draft') === $k ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select mb-3">
                    <option value="">— none —</option>
                    <?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($p['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
                </select>
                <label class="form-label">Brand</label>
                <select name="brand_id" class="form-select mb-3">
                    <option value="">— none —</option>
                    <?php foreach ($brands as $b): ?><option value="<?= (int) $b['id'] ?>" <?= (int) ($p['brand_id'] ?? 0) === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
                </select>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" id="ff" <?= !empty($p['is_featured']) ? 'checked' : '' ?>><label class="form-check-label" for="ff">Featured</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="is_trending" id="ft" <?= !empty($p['is_trending']) ? 'checked' : '' ?>><label class="form-check-label" for="ft">Trending</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="is_deal" id="fd" <?= !empty($p['is_deal']) ? 'checked' : '' ?>><label class="form-check-label" for="fd">Today's Deal</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="is_bestseller" id="fb" <?= !empty($p['is_bestseller']) ? 'checked' : '' ?>><label class="form-check-label" for="fb">Best Seller</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="is_new" id="fn" <?= !empty($p['is_new']) ? 'checked' : '' ?>><label class="form-check-label" for="fn">New</label></div>
            </div>

            <div class="card card-body mb-3">
                <h2 class="h6">Pricing</h2>
                <div class="mb-2"><label class="form-label small">Price (₹)</label><input name="price" type="number" step="0.01" class="form-control" value="<?= $val('price') ?>"></div>
                <div class="mb-2"><label class="form-label small">Original Price (₹)</label><input name="original_price" type="number" step="0.01" class="form-control" value="<?= $val('original_price') ?>"></div>
                <div class="mb-2"><label class="form-label small">Discount % (auto if blank)</label><input name="discount" type="number" class="form-control" value="<?= $val('discount') ?>"></div>
            </div>

            <div class="card card-body mb-3">
                <h2 class="h6">Affiliate Links</h2>
                <div class="mb-2"><label class="form-label small"><i class="fa-brands fa-amazon me-1"></i>Amazon</label><input name="amazon_link" type="url" class="form-control form-control-sm" value="<?= $val('amazon_link') ?>"></div>
                <div class="mb-2"><label class="form-label small">Flipkart</label><input name="flipkart_link" type="url" class="form-control form-control-sm" value="<?= $val('flipkart_link') ?>"></div>
                <div class="mb-2"><label class="form-label small">Meesho</label><input name="meesho_link" type="url" class="form-control form-control-sm" value="<?= $val('meesho_link') ?>"></div>
                <div class="mb-2"><label class="form-label small">Custom</label><input name="custom_link" type="url" class="form-control form-control-sm" value="<?= $val('custom_link') ?>"></div>
            </div>

            <div class="card card-body mb-3">
                <h2 class="h6">Gallery</h2>
                <?php if (!empty($images)): ?>
                    <div class="d-flex gap-2 flex-wrap mb-2"><?php foreach ($images as $img): ?><img src="<?= uploaded($img['path']) ?>" class="admin-thumb" alt=""><?php endforeach; ?></div>
                <?php endif; ?>
                <input type="file" name="gallery[]" class="form-control form-control-sm" accept="image/*" multiple>
                <small class="text-muted">JPG/PNG/WebP, up to 8 images.</small>
            </div>

            <button class="btn btn-brand w-100"><i class="fa-solid fa-floppy-disk me-1"></i>Save Product</button>
        </div>
    </div>
</form>

<script>
document.querySelectorAll('[data-add]').forEach(btn=>btn.addEventListener('click',()=>{
    const type=btn.dataset.add, wrap=document.getElementById(type+'Rows'), row=wrap.querySelector('.'+type+'-row');
    const clone=row.cloneNode(true); clone.querySelectorAll('input').forEach(i=>i.value=''); wrap.appendChild(clone);
}));
document.addEventListener('click',e=>{ if(e.target.classList.contains('remove-row')){ const rows=e.target.closest('div[id$=Rows], #specRows, #faqRows')||e.target.closest('.spec-row,.faq-row').parentElement; if(e.target.closest('.spec-row,.faq-row').parentElement.children.length>1) e.target.closest('.spec-row,.faq-row').remove(); }});
</script>
