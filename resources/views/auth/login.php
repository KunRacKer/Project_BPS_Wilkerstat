<?php
/** @var string|null $error */
/** @var string $old_username */
$appName = \App\Support\Config::get('app.name', 'SIMAP Wilkerstat');
?>
<div class="text-center mb-4">
    <div class="brand-badge mx-auto mb-3">
        <i class="bi bi-geo-alt-fill"></i>
    </div>
    <h1 class="h4 fw-semibold mb-1"><?= e($appName) ?></h1>
    <p class="text-secondary small mb-0">Badan Pusat Statistik Kabupaten Pidie Jaya</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?>
    </div>
<?php endif; ?>
<?php if (!empty($info)): ?>
    <div class="alert alert-info py-2 small" role="alert">
        <i class="bi bi-info-circle me-1"></i><?= e($info) ?>
    </div>
<?php endif; ?>
<?php if (!empty($warning)): ?>
    <div class="alert alert-warning py-2 small" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i><?= e($warning) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= e(base_url('login')) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="username" class="form-label small fw-semibold">Username</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="username" name="username"
                   value="<?= e($old_username) ?>" autocomplete="username" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password"
                   autocomplete="current-password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
    </button>
</form>

<div class="text-center small text-secondary mt-4">
    Lupa password? Hubungi <strong>Super Admin</strong>.
</div>