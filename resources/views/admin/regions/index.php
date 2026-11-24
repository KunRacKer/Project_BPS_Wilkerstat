<?php /** @var string $title */ /** @var array $children */ /** @var array $breadcrumb */ /** @var string|null $nextLevel */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <?php if ($nextLevel): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah <?= ucfirst($nextLevel) ?>
        </button>
    <?php endif; ?>
</div>

<!-- Breadcrumb Navigasi -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-body-tertiary p-3 rounded border">
        <li class="breadcrumb-item">
            <a href="<?= e(base_url('admin/regions')) ?>" class="<?= empty($breadcrumb) ? 'text-dark fw-bold' : '' ?>">
                <i class="bi bi-globe me-1"></i> Indonesia
            </a>
        </li>
        <?php foreach ($breadcrumb as $bc): ?>
            <li class="breadcrumb-item <?= end($breadcrumb)['id'] === $bc['id'] ? 'active fw-bold' : '' ?>">
                <a href="<?= e(base_url('admin/regions?parent=' . $bc['id'])) ?>"><?= e($bc['nama']) ?></a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($children)): ?>
            <?= \App\Support\View::partial('components.empty-state', [
                'icon' => 'folder2-open',
                'title' => 'Tidak Ada Data',
                'desc' => 'Belum ada wilayah di tingkat ini. Silakan tambah data baru.'
            ]) ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Wilayah</th>
                            <th>Kode</th>
                            <th>Level</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($children as $item): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if (in_array($item['level'], ['provinsi', 'kabupaten', 'kecamatan', 'desa'])): ?>
                                    <a href="<?= e(base_url('admin/regions?parent=' . $item['id'])) ?>" class="text-decoration-none fw-medium">
                                        <i class="bi bi-folder-fill text-warning me-2"></i><?= e($item['nama']) ?>
                                    </a>
                                <?php else: ?>
                                    <i class="bi bi-geo-alt-fill text-danger me-2"></i><?= e($item['nama']) ?>
                                <?php endif; ?>
                            </td>
                            <td><code><?= e($item['kode']) ?></code></td>
                            <td><span class="badge text-bg-secondary"><?= e(ucfirst($item['level'])) ?></span></td>
                            <td class="text-end pe-4">
                                <form method="post" action="<?= e(base_url('admin/regions/delete')) ?>" class="d-inline" onsubmit="return confirm('Hapus wilayah ini? Data anak (jika ada) mungkin akan terpengaruh.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="parent_id" value="<?= $currentId ?>">
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Wilayah -->
<?php if ($nextLevel): ?>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= e(base_url('admin/regions')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="parent_id" value="<?= $currentId ?? '' ?>">
                <input type="hidden" name="level" value="<?= e($nextLevel) ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Tambah <?= ucfirst($nextLevel) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Wilayah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode" required placeholder="Contoh: 11, 11.01, 11.01.01">
                        <div class="form-text">Kode harus unik di seluruh sistem.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama <?= ucfirst($nextLevel) ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>