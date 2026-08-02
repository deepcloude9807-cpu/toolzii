<?php
$settings = $settings ?? [];
$navCategories = $navCategories ?? [];
?>
<footer class="toolzy-footer mt-5 pt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="brand-mark mb-3">Toolzy<span>Net</span></h5>
                <p class="text-muted"><?= e($settings['tagline'] ?? 'Compare. Review. Buy Smart.') ?></p>
                <div class="social-links">
                    <?php foreach (['facebook' => 'facebook-f', 'twitter' => 'x-twitter', 'instagram' => 'instagram', 'youtube' => 'youtube'] as $key => $icon): ?>
                        <?php if (!empty($settings['social_' . $key])): ?>
                            <a href="<?= e($settings['social_' . $key]) ?>" target="_blank" rel="noopener nofollow" aria-label="<?= e($key) ?>"><i class="fa-brands fa-<?= e($icon) ?>"></i></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Categories</h6>
                <ul class="list-unstyled footer-links">
                    <?php foreach (array_slice($navCategories, 0, 6) as $cat): ?>
                        <li><a href="<?= url('category/' . $cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Company</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('about') ?>">About</a></li>
                    <li><a href="<?= url('contact') ?>">Contact</a></li>
                    <li><a href="<?= url('blog') ?>">Blog</a></li>
                    <li><a href="<?= url('disclosure') ?>">Affiliate Disclosure</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Legal</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('privacy-policy') ?>">Privacy Policy</a></li>
                    <li><a href="<?= url('terms') ?>">Terms</a></li>
                    <li><a href="<?= url('sitemap.xml') ?>">Sitemap</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Newsletter</h6>
                <form id="newsletterForm" class="newsletter-form">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="Your email" required>
                        <button class="btn btn-brand btn-sm" type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                    <small class="form-text" id="newsletterMsg"></small>
                </form>
            </div>
        </div>
        <hr class="mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pb-4 small text-muted">
            <span><?= e($settings['footer_copyright'] ?? ('© ' . date('Y') . ' ToolzyNet.com')) ?></span>
            <span>As an affiliate, we may earn from qualifying purchases.</span>
        </div>
    </div>
</footer>
