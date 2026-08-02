<div class="container py-5 text-center error-page">
    <div class="error-code">404</div>
    <h1 class="h4">Page not found</h1>
    <p class="text-muted"><?= e($message ?? "The page you're looking for doesn't exist or has moved.") ?></p>
    <a href="<?= url('') ?>" class="btn btn-brand mt-2"><i class="fa-solid fa-house me-1"></i>Back to Home</a>
</div>
