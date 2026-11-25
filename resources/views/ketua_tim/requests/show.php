<?php /** @var string $title */ /** @var array $request */ /** @var array $items */ ?>

<div class="d-flex gap-2">
    <a href="<?= e(base_url('app/requests')) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <?php if ($request['status'] === 'processing' || $request['status'] === 'completed'): ?>
        <a href="<?= e(base_url('app/requests/' . $request['id'] . '/downloads')) ?>" class="btn btn-primary">
            <i class="bi bi-download me-1"></i> Unduh File
        </a>
    <?php endif; ?>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="text-secondary small mb-1">No. Permintaan</h6>
                <h5 class="fw-bold text-primary mb-3"><?= e($request['request_number']) ?></h5>
                
                <h6 class="text-secondary small mb-1">Tanggal Pengajuan</h6>
                <p class="fw-medium mb-3"><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></p>
                
                <h6 class="text-secondary small mb-1">Layanan Diminta</h6>
                <div class="d-flex gap-2 mb-3">
                    <?php if ($request['need_download']): ?>
                        <span class="badge text-bg-info">Unduh File</span>
                    <?php endif; ?>
                    <?php if ($request['need_print']): ?>
                        <span class="badge text-bg-warning">Cetak (<?= $request['print_copies'] ?>x)</span>
                    <?php endif; ?>
                </div>
                
                <h6 class="text-secondary small mb-1">Status Permintaan</h6>
                <div class="mb-3">
                    <?= \App\Support\View::partial('components.status-badge', ['status' => $request['status']]) ?>
                </div>

                <h6 class="text-secondary small mb-1">Catatan Anda</h6>
                <div class="bg-body-tertiary p-2 rounded small border mb-0">
                    <?= nl2br(e($request['notes'] ?? '-')) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0">Daftar ID Wilkerstat (<?= count($items) ?> item)</h6>
                <p class="text-secondary small">Berikut adalah status persetujuan untuk setiap ID yang Anda ajukan.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID & Nama SLS</th>
                                <th>Status</th>
                                <th>Alasan (Jika Ditolak/Ditunda)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold font-monospace"><?= e($item['id_wilkerstat']) ?></div>
                                    <div class="small text-secondary"><?= e($item['nama_sls']) ?></div>
                                </td>
                                <td>
                                    <?= \App\Support\View::partial('components.status-badge', ['status' => $item['status']]) ?>
                                </td>
                                <td class="text-secondary small">
                                    <?= e($item['reject_reason'] ?? '-') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>