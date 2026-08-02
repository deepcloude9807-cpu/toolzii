<?php
/** @var string $content */
$seo = $seo ?? [];
$settings = $settings ?? [];
$themeColor = $settings['theme_color'] ?? '#2563eb';
$darkDefault = ($settings['dark_mode'] ?? '0') === '1';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= $darkDefault ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seo['title'] ?? ($settings['site_name'] ?? 'ToolzyNet')) ?></title>
    <meta name="description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="robots" content="<?= e($seo['robots'] ?? 'index, follow') ?>">
    <link rel="canonical" href="<?= e($seo['canonical'] ?? url('')) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="<?= e($seo['type'] ?? 'website') ?>">
    <meta property="og:title" content="<?= e($seo['title'] ?? '') ?>">
    <meta property="og:description" content="<?= e($seo['description'] ?? '') ?>">
    <meta property="og:url" content="<?= e($seo['canonical'] ?? url('')) ?>">
    <meta property="og:image" content="<?= e($seo['og_image'] ?? asset('img/og-default.svg')) ?>">
    <meta property="og:site_name" content="<?= e($settings['site_name'] ?? 'ToolzyNet') ?>">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($seo['title'] ?? '') ?>">
    <meta name="twitter:description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= e($seo['og_image'] ?? asset('img/og-default.svg')) ?>">

    <link rel="icon" href="<?= e(isset($settings['favicon']) ? uploaded($settings['favicon']) : asset('img/placeholder.svg')) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <style>:root{ --brand:<?= e($themeColor) ?>; }</style>
    <?php if (!empty($seo['schema'])): ?><?= $seo['schema'] ?><?php endif; ?>
</head>
<body>
<?php \App\Core\View::partial('partials/header'); ?>

<main>
    <?php \App\Core\View::partial('partials/flash'); ?>
    <?= $content ?>
</main>

<?php \App\Core\View::partial('partials/footer'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.TOOLZY_BASE = "<?= e(rtrim((string) env('APP_URL', ''), '/')) ?>"; window.CSRF = "<?= e($csrf ?? '') ?>";</script>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
