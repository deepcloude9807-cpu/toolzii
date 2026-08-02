<form method="post" action="<?= url('admin/forgot-password') ?>">
    <?= csrf_field() ?>
    <p class="small text-muted">Enter your email and we'll send a reset link.</p>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required autofocus></div>
    <button class="btn btn-brand w-100">Send Reset Link</button>
    <div class="text-center mt-3"><a href="<?= url('admin/login') ?>" class="small">Back to login</a></div>
</form>
