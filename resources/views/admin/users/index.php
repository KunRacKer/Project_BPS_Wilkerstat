<?php /** @var string $title */ /** @var array $paginator */ /** @var string $search */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <a href="<?= e(base_url('admin/users/create')) ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-body border-bottom-0 pt-3 pb-0">
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari nama atau username..." value="<?= e($search) ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-secondary">Cari</button>
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <?php if (empty($paginator['data'])): ?>
            <?= \App\Support\View::partial('components.empty-state', [
                'icon' => 'people',
                'title' => 'Tidak Ada Pengguna',
                'desc' => 'Belum ada data Ketua Tim atau pencarian tidak cocok.'
            ]) ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Lengkap</th>
                            <th>Username</th>
                            <th>WhatsApp</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginator['data'] as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium"><?= e($u['full_name'] ?? '-') ?></div>
                            </td>
                            <td><?= e($u['username']) ?></td>
                            <td><?= e($u['whatsapp'] ?? '-') ?></td>
                            <td>
                                <?php if ($u['is_active']): ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown" data-bs-boundary="window" data-bs-display="static">
    <i class="bi bi-three-dots"></i>
</button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li>
                                            <a href="<?= e(base_url('admin/users/' . $u['id'] . '/edit')) ?>" class="dropdown-item">
                                                <i class="bi bi-pencil me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form method="post" action="<?= e(base_url('admin/users/' . $u['id'] . '/toggle')) ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-power me-2"></i> <?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="post" action="<?= e(base_url('admin/users/' . $u['id'] . '/reset-password')) ?>" onsubmit="return confirm('Reset password user ini? Password baru akan ditampilkan.')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="bi bi-key me-2"></i> Reset Password
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginasi -->
    <?php if ($paginator['last_page'] > 1): ?>
    <div class="card-footer bg-body border-top-0 py-3 d-flex justify-content-between align-items-center">
        <div class="text-secondary small">
            Menampilkan <?= count($paginator['data']) ?> dari total <?= $paginator['total'] ?> pengguna
        </div>
        <ul class="pagination pagination-sm mb-0">
            <?php for ($i = 1; $i <= $paginator['last_page']; $i++): ?>
                <li class="page-item <?= $i === $paginator['page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>