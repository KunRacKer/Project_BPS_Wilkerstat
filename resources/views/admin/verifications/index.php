<?php /** @var string $title */ /** @var array $requests */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($requests)): ?>
            <?= \App\Support\View::partial('components.empty-state', [
                'icon' => 'check2-circle',
                'title' => 'Antrean Kosong',
                'desc' => 'Saat ini tidak ada permintaan yang menunggu verifikasi.'
            ]) ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Permintaan</th>
                            <th>Pemohon</th>
                            <th>Tanggal Diajukan</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-primary"><?= e($req['request_number']) ?></td>
                            <td><?= e($req['requester_name'] ?? '-') ?></td>
                            <td class="text-secondary small"><?= date('d/m/Y H:i', strtotime($req['submitted_at'])) ?></td>
                            <td>
                                <?= \App\Support\View::partial('components.status-badge', ['status' => $req['status']]) ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= e(base_url('admin/verifications/' . $req['id'])) ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-check2-square"></i> Verifikasi
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>