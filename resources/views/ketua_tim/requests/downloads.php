<?php /** @var string $title */ /** @var array $request */ /** @var array $downloads */ /** @var string $csrf_token */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <a href="<?= e(base_url('app/requests/' . $request['id'])) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
    </a>
</div>

<div class="alert alert-info d-flex align-items-center gap-2">
    <i class="bi bi-info-circle fs-4"></i>
    <div>
        <strong>Permintaan: <?= e($request['request_number']) ?></strong><br>
        Berikut adalah daftar file yang siap diunduh dari ID yang telah disetujui.
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Buat Paket Unduhan Baru</h6>
                <form method="post" action="<?= e(base_url('app/requests/' . $request['id'] . '/downloads')) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Format</label>
                        <select name="type" class="form-select">
                            <option value="zip">ZIP (Semua File)</option>
                            <option value="jpg">JPG Saja</option>
                            <option value="rar">RAR Saja</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-file-earmark-zip me-1"></i> Siapkan File
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">Riwayat Unduhan</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($downloads)): ?>
                    <?= \App\Support\View::partial('components.empty-state', [
                        'icon' => 'download',
                        'title' => 'Belum Ada Paket Unduhan',
                        'desc' => 'Silakan buat paket unduhan baru dari panel kiri.'
                    ]) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal Dibuat</th>
                                    <th>Format</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($downloads as $dl): ?>
                                <tr>
                                    <td class="ps-4 text-secondary small"><?= date('d/m/Y H:i', strtotime($dl['created_at'])) ?></td>
                                    <td><span class="badge text-bg-secondary"><?= strtoupper($dl['download_type']) ?></span></td>
                                    <td>
                                        <?php if ($dl['status'] === 'ready'): ?>
                                            <span class="badge text-bg-success">Siap</span>
                                        <?php elseif ($dl['status'] === 'preparing'): ?>
                                            <span class="badge text-bg-warning"><i class="spinner-border spinner-border-sm me-1"></i>Menyiapkan...</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-danger"><?= ucfirst($dl['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($dl['status'] === 'ready'): ?>
                                            <a href="<?= e(base_url('download.php?token=' . $dl['token_plain'])) ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>