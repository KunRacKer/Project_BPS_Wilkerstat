<?php /** @var string $title */ /** @var array $stats */ ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <div class="text-secondary small"><?= date('d F Y') ?></div>
</div>

<div class="row g-3 mb-4">
    <!-- Card 1 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-dashboard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1"><?= number_format($stats['total_ketua_tim']) ?></h2>
                <div class="text-secondary small">Total Ketua Tim</div>
            </div>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-dashboard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info rounded">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1"><?= number_format($stats['total_requests']) ?></h2>
                <div class="text-secondary small">Total Permintaan</div>
            </div>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-dashboard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1"><?= number_format($stats['pending_verifications']) ?></h2>
                <div class="text-secondary small">Menunggu Verifikasi</div>
            </div>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-dashboard">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded">
                        <i class="bi bi-printer"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1"><?= number_format($stats['print_queue']) ?></h2>
                <div class="text-secondary small">Antrean Cetak</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-body border-bottom-0 pt-3 pb-2">
                <h5 class="card-title fw-semibold mb-0">Permintaan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($stats['recent_requests'])): ?>
                    <?= \App\Support\View::partial('components.empty-state', [
                        'icon' => 'inbox',
                        'title' => 'Belum Ada Permintaan',
                        'desc' => 'Data permintaan peta akan muncul di sini.'
                    ]) ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Permintaan</th>
                                    <th>Pemohon</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_requests'] as $req): ?>
                                <tr>
                                    <td class="ps-3 fw-medium text-primary"><?= e($req['request_number']) ?></td>
                                    <td><?= e($req['requester_name'] ?? '-') ?></td>
                                    <td class="text-secondary small"><?= date('d/m/Y', strtotime($req['created_at'])) ?></td>
                                    <td>
                                        <?= \App\Support\View::partial('components.status-badge', ['status' => $req['status']]) ?>
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
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center text-center py-5">
                <div class="text-secondary mb-3">
                    <i class="bi bi-info-circle fs-1 opacity-50"></i>
                </div>
                <h6 class="fw-semibold">Sistem Berjalan Normal</h6>
                <p class="text-secondary small mb-0">Core system dan koneksi database terhubung dengan baik.</p>
            </div>
        </div>
    </div>
</div>