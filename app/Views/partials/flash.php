<?php $flash = $flash ?? []; ?>
<?php if (!empty($flash['success'])): ?>
    <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-1"></i><?= e($flash['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-1"></i><?= e($flash['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div></div>
<?php endif; ?>
