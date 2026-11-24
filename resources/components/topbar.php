<?php
/** @var array $user */
$appName = \App\Support\Config::get('app.name', 'SIMAP Wilkerstat');
$roleLabel = ($user['role'] ?? '') === 'super_admin' ? 'Super Admin' : 'Ketua Tim';
$dashboard = ($user['role'] ?? '') === 'super_admin' ? 'admin/dashboard' : 'app/dashboard';
?>
<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top shadow-sm">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="<?= e(base_url($dashboard)) ?>">
            <span class="brand-badge"><i class="bi bi-geo-alt-fill"></i></span>
            <span class="d-none d-sm-inline"><?= e($appName) ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= e(base_url($dashboard)) ?>">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <?= \App\Support\View::partial('components.theme-toggle') ?>

                <div class="dropdown">
                    <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-md-inline small"><?= e($user['full_name'] ?? $user['username'] ?? '') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><span class="dropdown-item-text small text-secondary"><?= e($roleLabel) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= e(base_url('logout')) ?>" class="px-2">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>