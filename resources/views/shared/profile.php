<?php /** @var string $title */ /** @var array $user */ /** @var bool $isAdmin */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <a href="<?= e(base_url($isAdmin ? 'admin/dashboard' : 'app/dashboard')) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Detail Profil -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center pt-4">
                <div class="mb-3">
                    <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?= e(strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1))) ?>
                    </div>
                </div>
                <h5 class="fw-bold mb-1"><?= e($user['full_name'] ?? 'Belum diatur') ?></h5>
                <div class="text-secondary small mb-3">@<?= e($user['username']) ?></div>
                
                <div class="badge text-bg-primary mb-4 px-3 py-2">
                    <?= $isAdmin ? 'Super Admin' : 'Ketua Tim' ?>
                </div>

                <div class="text-start border-top pt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">No. WhatsApp</span>
                        <span class="small fw-medium"><?= e($user['whatsapp'] ?? '-') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Terakhir Login</span>
                        <span class="small fw-medium"><?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Edit -->
    <div class="col-lg-8">
        <!-- Form Update Data -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">Informasi Akun</h6>
                <p class="text-secondary small">Perbarui nama dan nomor WhatsApp Anda.</p>
            </div>
            <div class="card-body">
                <form method="post" action="<?= e(base_url($isAdmin ? 'admin/profile/update' : 'app/profile/update')) ?>">
                    <?= csrf_field() ?>
                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'full_name',
                        'label' => 'Nama Lengkap',
                        'value' => $user['full_name'] ?? '',
                        'required' => true,
                        'placeholder' => 'Nama Lengkap'
                    ]) ?>
                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'whatsapp',
                        'label' => 'Nomor WhatsApp',
                        'value' => $user['whatsapp'] ?? '',
                        'placeholder' => 'Contoh: 6281234567890',
                        'help' => 'Gunakan format 62 (tanpa tanda + atau spasi).'
                    ]) ?>
                    
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Ganti Password -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">Ganti Password</h6>
                <p class="text-secondary small">Abaikan bagian ini jika tidak ingin mengganti password.</p>
            </div>
            <div class="card-body">
                <form method="post" action="<?= e(base_url($isAdmin ? 'admin/profile/password' : 'app/profile/password')) ?>">
                    <?= csrf_field() ?>
                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'current_password',
                        'label' => 'Password Saat Ini',
                        'type' => 'password',
                        'required' => true,
                    ]) ?>
                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'new_password',
                        'label' => 'Password Baru',
                        'type' => 'password',
                        'required' => true,
                        'help' => 'Minimal 8 karakter.'
                    ]) ?>
                    
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-key me-1"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>