<?php $settings = $settings ?? []; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h1 class="h3 mb-1">Contact Us</h1>
            <p class="text-muted">Questions, partnerships or feedback — we'd love to hear from you.</p>
            <?php if (!empty($settings['contact_email'])): ?>
                <p><i class="fa-solid fa-envelope me-2 text-brand"></i><?= e($settings['contact_email']) ?></p>
            <?php endif; ?>
            <form method="post" action="<?= url('contact') ?>" class="card card-body">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Subject</label><input name="subject" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
                    <div class="col-12"><button class="btn btn-brand">Send Message</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
