<?php
use App\Core\Auth;
/** @var string $content */
$user = Auth::user() ?? ['name' => 'Admin', 'role' => 'admin'];
$uri = $_SERVER['REQUEST_URI'] ?? '';
$active = fn (string $path) => str_starts_with($uri, $path) ? 'active' : '';
$flash = $flash ?? [];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin — <?= e($settings['site_name'] ?? 'ToolzyNet') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand"><a href="<?= url('admin/dashboard') ?>">Toolzy<span>Net</span> <small>Admin</small></a></div>
        <nav class="sidebar-nav">
            <a href="<?= url('admin/dashboard') ?>" class="<?= $active('/admin/dashboard') ?><?= $uri === '/admin' ? 'active' : '' ?>"><i class="fa-solid fa-gauge-high fa-fw"></i> Dashboard</a>
            <a href="<?= url('admin/products') ?>" class="<?= $active('/admin/products') ?>"><i class="fa-solid fa-box fa-fw"></i> Products</a>
            <a href="<?= url('admin/categories') ?>" class="<?= $active('/admin/categories') ?>"><i class="fa-solid fa-layer-group fa-fw"></i> Categories</a>
            <a href="<?= url('admin/brands') ?>" class="<?= $active('/admin/brands') ?>"><i class="fa-solid fa-tags fa-fw"></i> Brands</a>
            <a href="<?= url('admin/blog') ?>" class="<?= $active('/admin/blog') ?>"><i class="fa-solid fa-newspaper fa-fw"></i> Blog</a>
            <a href="<?= url('admin/analytics') ?>" class="<?= $active('/admin/analytics') ?>"><i class="fa-solid fa-chart-line fa-fw"></i> Analytics</a>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="<?= url('admin/settings') ?>" class="<?= $active('/admin/settings') ?>"><i class="fa-solid fa-gear fa-fw"></i> Settings</a>
            <?php endif; ?>
            <hr>
            <a href="<?= url('') ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square fa-fw"></i> View Site</a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="badge bg-secondary text-capitalize"><?= e($user['role']) ?></span>
                <span class="fw-semibold"><?= e($user['name']) ?></span>
                <form method="post" action="<?= url('admin/logout') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket"></i></button></form>
            </div>
        </header>

        <div class="admin-content">
            <?php if (!empty($flash['success'])): ?><div class="alert alert-success alert-dismissible fade show"><?= e($flash['success']) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if (!empty($flash['error'])): ?><div class="alert alert-danger alert-dismissible fade show"><?= e($flash['error']) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?= $content ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.getElementById('sidebarToggle')?.addEventListener('click',()=>document.getElementById('adminSidebar').classList.toggle('open'));</script>
</body>
</html>
