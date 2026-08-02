<h1 class="h4 mb-3">Bulk Import Products (CSV)</h1>
<div class="card card-body" style="max-width:640px">
    <p class="small text-muted">Upload a CSV with a header row. Supported columns:</p>
    <code class="d-block mb-3 p-2 bg-light rounded">name, category, brand, price, original_price, amazon_link, short_description, status</code>
    <form method="post" action="<?= url('admin/products/import') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-3"><input type="file" name="csv" class="form-control" accept=".csv,text/csv" required></div>
        <button class="btn btn-brand"><i class="fa-solid fa-upload me-1"></i>Import</button>
        <a href="<?= url('admin/products') ?>" class="btn btn-link">Cancel</a>
    </form>
</div>
