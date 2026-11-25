<?php
/** @var array $user */
$appName = \App\Support\Config::get('app.name', 'SIMAP Wilkerstat');
$roleLabel = ($user['role'] ?? '') === 'super_admin' ? 'Super Admin' : 'Ketua Tim';
$dashboardUrl = ($user['role'] ?? '') === 'super_admin' ? 'admin/dashboard' : 'app/dashboard';
?>
<nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top shadow-sm topbar-app">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="<?= e(base_url($dashboardUrl)) ?>">
            <span class="brand-badge"><i class="bi bi-geo-alt-fill"></i></span>
            <span class="d-none d-sm-inline"><?= e($appName) ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topbarNav">
            <!-- Navigasi Utama (Opsional: Bisa Ditambah Nanti) -->
            <!-- Di dalam tag <ul class="navbar-nav me-auto mb-2 mb-lg-0">, ubah menjadi: -->
<ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <li class="nav-item">
        <a class="nav-link" href="<?= e(base_url($dashboardUrl)) ?>">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
        </a>
    </li>
    <?php if (($user['role'] ?? '') === 'super_admin'): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= e(base_url('admin/users')) ?>">
            <i class="bi bi-people me-1"></i> Pengguna
        </a>
    </li>
    <!-- Di dalam <ul class="navbar-nav me-auto mb-2 mb-lg-0">, tambahkan setelah menu Pengguna -->
    <li class="nav-item">
        <a class="nav-link" href="<?= e(base_url('admin/regions')) ?>">
            <i class="bi bi-map me-1"></i> Wilayah
        </a>
    </li>
    <?php endif; ?>
    <!-- Di dalam <ul class="navbar-nav me-auto mb-2 mb-lg-0"> -->
    <?php if (($user['role'] ?? '') === 'ketua_tim'): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= e(base_url('app/requests')) ?>">
            <i class="bi bi-file-earmark-text me-1"></i> Permintaan
        </a>
    </li>
    <?php endif; ?>

    <?php if (($user['role'] ?? '') === 'super_admin'): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= e(base_url('admin/verifications')) ?>">
            <i class="bi bi-check2-square me-1"></i> Verifikasi
        </a>
    </li>
    <?php endif; ?>
</ul>

            <!-- Menu Kanan: Notifikasi & User -->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <?= \App\Support\View::partial('components.theme-toggle') ?>
                
                <!-- Notifikasi Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                            0
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow notif-dropdown p-0" style="width: 320px;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-body-tertiary">
                            <span class="fw-semibold small">Notifikasi</span>
                            <span class="badge text-bg-secondary">0 Baru</span>
                        </li>
                        <li>
                            <div class="text-center py-4 text-secondary small">
                                <i class="bi bi-bell-slash fs-4 d-block mb-2 opacity-50"></i>
                                Belum ada notifikasi
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-md-inline small"><?= e($user['full_name'] ?? $user['username'] ?? '') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><span class="dropdown-item-text small text-secondary">Masuk sebagai<br><strong><?= e($roleLabel) ?></strong></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
    <a class="dropdown-item" href="<?= e(base_url($dashboardUrl === 'admin/dashboard' ? 'admin/profile' : 'app/profile')) ?>">
        <i class="bi bi-person me-2"></i> Profil Saya
    </a>
</li>
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