<?php /** @var string $content */ $flash = $flash ?? []; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login — <?= e($settings['site_name'] ?? 'ToolzyNet') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="text-center mb-4"><h1 class="brand-mark h3">Toolzy<span>Net</span></h1><p class="text-muted small">Admin Panel</p></div>
        <?php if (!empty($flash['success'])): ?><div class="alert alert-success py-2"><?= e($flash['success']) ?></div><?php endif; ?>
        <?php if (!empty($flash['error'])): ?><div class="alert alert-danger py-2"><?= e($flash['error']) ?></div><?php endif; ?>
        <?= $content ?>
    </div>
</body>
</html>
