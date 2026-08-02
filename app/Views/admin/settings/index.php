<?php
/** @var array $settings */
$s = fn (string $k, string $d = '') => e($settings[$k] ?? $d);
$on = fn (string $k) => ($settings[$k] ?? '0') === '1' ? 'checked' : '';
?>
<h1 class="h4 mb-3">Settings</h1>
<form method="post" action="<?= url('admin/settings') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-lg-6"><div class="card card-body">
            <h2 class="h6">General</h2>
            <div class="mb-2"><label class="form-label small">Site Name</label><input name="site_name" class="form-control" value="<?= $s('site_name', 'ToolzyNet') ?>"></div>
            <div class="mb-2"><label class="form-label small">Tagline</label><input name="tagline" class="form-control" value="<?= $s('tagline') ?>"></div>
            <div class="mb-2"><label class="form-label small">Contact Email</label><input name="contact_email" type="email" class="form-control" value="<?= $s('contact_email') ?>"></div>
            <div class="mb-2"><label class="form-label small">Footer Copyright</label><input name="footer_copyright" class="form-control" value="<?= $s('footer_copyright') ?>"></div>
            <div class="form-check form-switch mt-2"><input class="form-check-input" type="checkbox" name="maintenance_mode" id="mm" <?= $on('maintenance_mode') ?>><label class="form-check-label" for="mm">Maintenance Mode</label></div>
        </div></div>

        <div class="col-lg-6"><div class="card card-body">
            <h2 class="h6">SEO Defaults</h2>
            <div class="mb-2"><label class="form-label small">Default Meta Title</label><input name="meta_title" class="form-control" value="<?= $s('meta_title') ?>"></div>
            <div class="mb-2"><label class="form-label small">Default Meta Description</label><textarea name="meta_description" class="form-control" rows="3"><?= $s('meta_description') ?></textarea></div>
            <p class="small text-muted mb-0">Sitemap: <a href="<?= url('sitemap.xml') ?>" target="_blank">/sitemap.xml</a> · Robots: <a href="<?= url('robots.txt') ?>" target="_blank">/robots.txt</a></p>
        </div></div>

        <div class="col-lg-6"><div class="card card-body">
            <h2 class="h6">Appearance</h2>
            <div class="mb-2"><label class="form-label small">Theme Color</label><input name="theme_color" type="color" class="form-control form-control-color" value="<?= $s('theme_color', '#2563eb') ?>"></div>
            <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="dark_mode" id="dm" <?= $on('dark_mode') ?>><label class="form-check-label" for="dm">Default Dark Mode</label></div>
            <div class="mb-2"><label class="form-label small">Logo</label><input type="file" name="logo" class="form-control" accept="image/*"><?php if (!empty($settings['logo'])): ?><img src="<?= uploaded($settings['logo']) ?>" height="30" class="mt-1" alt=""><?php endif; ?></div>
            <div class="mb-2"><label class="form-label small">Favicon</label><input type="file" name="favicon" class="form-control" accept="image/*"></div>
        </div></div>

        <div class="col-lg-6"><div class="card card-body">
            <h2 class="h6">Social Links</h2>
            <div class="mb-2"><label class="form-label small">Facebook</label><input name="social_facebook" class="form-control" value="<?= $s('social_facebook') ?>"></div>
            <div class="mb-2"><label class="form-label small">Twitter / X</label><input name="social_twitter" class="form-control" value="<?= $s('social_twitter') ?>"></div>
            <div class="mb-2"><label class="form-label small">Instagram</label><input name="social_instagram" class="form-control" value="<?= $s('social_instagram') ?>"></div>
            <div class="mb-2"><label class="form-label small">YouTube</label><input name="social_youtube" class="form-control" value="<?= $s('social_youtube') ?>"></div>
        </div></div>

        <div class="col-lg-6"><div class="card card-body">
            <h2 class="h6">SMTP (Mail)</h2>
            <div class="mb-2"><label class="form-label small">Host</label><input name="smtp_host" class="form-control" value="<?= $s('smtp_host') ?>"></div>
            <div class="row g-2">
                <div class="col-6 mb-2"><label class="form-label small">Port</label><input name="smtp_port" class="form-control" value="<?= $s('smtp_port') ?>"></div>
                <div class="col-6 mb-2"><label class="form-label small">Username</label><input name="smtp_user" class="form-control" value="<?= $s('smtp_user') ?>"></div>
            </div>
            <small class="text-muted">Passwords/keys belong in <code>.env</code>, not the DB.</small>
        </div></div>
    </div>
    <div class="mt-3"><button class="btn btn-brand"><i class="fa-solid fa-floppy-disk me-1"></i>Save Settings</button></div>
</form>
