<?php
$settings = $settings ?? [];
$navCategories = $navCategories ?? [];
?>
<nav class="navbar navbar-expand-lg sticky-top toolzy-nav shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('') ?>">
            <?php if (!empty($settings['logo'])): ?>
                <img src="<?= uploaded($settings['logo']) ?>" alt="<?= e($settings['site_name'] ?? 'ToolzyNet') ?>" height="34">
            <?php else: ?>
                <span class="brand-mark">Toolzy<span>Net</span></span>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <form class="d-flex mx-lg-auto my-2 my-lg-0 position-relative toolzy-search" action="<?= url('search') ?>" method="get" role="search">
                <input class="form-control" type="search" name="q" id="globalSearch" placeholder="Search products, brands…" autocomplete="off" aria-label="Search">
                <button class="btn btn-brand" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <div class="autocomplete-panel" id="searchSuggest"></div>
            </form>
            <ul class="navbar-nav ms-lg-3 align-items-lg-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Categories</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach ($navCategories as $cat): ?>
                            <li><a class="dropdown-item" href="<?= url('category/' . $cat['slug']) ?>">
                                <i class="fa-solid <?= e($cat['icon'] ?: 'fa-tag') ?> fa-fw me-1"></i><?= e($cat['name']) ?>
                            </a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= url('compare') ?>">Compare</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('blog') ?>">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('contact') ?>">Contact</a></li>
                <li class="nav-item">
                    <button class="btn btn-sm btn-outline-secondary ms-lg-2" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
