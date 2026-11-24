<?php /** @var string $title */ /** @var array $user */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <a href="<?= e(base_url('admin/users')) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= e(base_url('admin/users/' . $user['id'] . '/edit')) ?>">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?= \App\Support\View::partial('components.form-group', [
                                'name' => 'username',
                                'label' => 'Username',
                                'value' => $user['username'],
                                'required' => true,
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= \App\Support\View::partial('components.form-group', [
                                'name' => 'full_name',
                                'label' => 'Nama Lengkap',
                                'value' => $user['full_name'] ?? '',
                                'required' => true
                            ]) ?>
                        </div>
                    </div>

                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'whatsapp',
                        'label' => 'Nomor WhatsApp',
                        'value' => $user['whatsapp'] ?? '',
                        'placeholder' => 'Contoh: 6281234567890'
                    ]) ?>
                    
                    <div class="alert alert-info py-2 small mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Password tidak dapat diubah dari halaman ini. Gunakan menu <strong>Reset Password</strong> di daftar pengguna.
                    </div>
                    
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>