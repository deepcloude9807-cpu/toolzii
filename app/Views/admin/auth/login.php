<form method="post" action="<?= url('admin/login') ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-brand w-100">Log In</button>
    <div class="text-center mt-3"><a href="<?= url('admin/forgot-password') ?>" class="small">Forgot password?</a></div>
</form>
