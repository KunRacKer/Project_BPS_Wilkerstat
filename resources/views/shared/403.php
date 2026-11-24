<?php /** @var string|null $reason */ ?>
<div class="text-center py-5">
    <div class="display-1 text-warning mb-3"><i class="bi bi-shield-lock"></i></div>
    <h1 class="h3 fw-semibold">Akses Ditolak</h1>
    <p class="text-secondary"><?= e($reason ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.') ?></p>
    <a href="<?= e(base_url('/')) ?>" class="btn btn-primary mt-2"><i class="bi bi-house me-1"></i>Kembali</a>
</div>