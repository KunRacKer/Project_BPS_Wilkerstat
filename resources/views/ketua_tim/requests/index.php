<?php /** @var string $title */ /** @var array $requests */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <a href="<?= e(base_url('app/requests/create')) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Permintaan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($requests)): ?>
            <?= \App\Support\View::partial('components.empty-state', [
                'icon' => 'file-earmark-text',
                'title' => 'Belum Ada Permintaan',
                'desc' => 'Anda belum pernah mengajukan permintaan peta.'
            ]) ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Permintaan</th>
                            <th>Tanggal</th>
                            <th>Item (Disetujui)</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-primary"><?= e($req['request_number']) ?></td>
                            <td class="text-secondary small"><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                            <td>
                                <span class="badge text-bg-secondary"><?= $req['total_items'] ?> ID</span>
                                <?php if ($req['approved_items'] > 0): ?>
                                    <span class="badge text-bg-success"><?= $req['approved_items'] ?> Disetujui</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= \App\Support\View::partial('components.status-badge', ['status' => $req['status']]) ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= e(base_url('app/requests/' . $req['id'])) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
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