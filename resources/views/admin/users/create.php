<?php /** @var string $title */ 
$old = \App\Support\Session::pullFlash('old', []);
?>

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
                <form method="post" action="<?= e(base_url('admin/users')) ?>">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?= \App\Support\View::partial('components.form-group', [
                                'name' => 'username',
                                'label' => 'Username',
                                'value' => $old['username'] ?? '',
                                'required' => true,
                                'placeholder' => 'Minimal 3 karakter',
                                'help' => 'Hanya huruf, angka, garis bawah, dan titik.'
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= \App\Support\View::partial('components.form-group', [
                                'name' => 'full_name',
                                'label' => 'Nama Lengkap',
                                'value' => $old['full_name'] ?? '',
                                'required' => true
                            ]) ?>
                        </div>
                    </div>

                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'whatsapp',
                        'label' => 'Nomor WhatsApp',
                        'value' => $old['whatsapp'] ?? '',
                        'placeholder' => 'Contoh: 6281234567890'
                    ]) ?>

                    <?= \App\Support\View::partial('components.form-group', [
                        'name' => 'password',
                        'label' => 'Password Awal',
                        'type' => 'password',
                        'required' => true,
                        'help' => 'Minimal 8 karakter. Sampaikan ke pengguna agar bisa diganti sendiri.'
                    ]) ?>
                    
                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>