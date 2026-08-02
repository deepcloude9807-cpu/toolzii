<?php /** @var string $token */ ?>
<form method="post" action="<?= url('admin/reset-password/' . e($token)) ?>">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" minlength="8" required autofocus></div>
    <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" minlength="8" required></div>
    <button class="btn btn-brand w-100">Update Password</button>
</form>
