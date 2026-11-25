<?php /** @var string $title */ ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0"><?= e($title) ?></h1>
    <a href="<?= e(base_url('app/requests')) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<form method="post" action="<?= e(base_url('app/requests')) ?>">
    <?= csrf_field() ?>
    <div class="row g-4">
        
        <!-- Kolom Kiri: Input ID -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">1. Masukkan ID Wilkerstat</h6>
                    
                    <div class="alert alert-info py-2 small mb-3">
                        Anda dapat mengetik ID secara manual, dipisahkan dengan <strong>koma</strong> atau <strong>enter</strong>.
                        Contoh: <code>1101010001, 1101010002</code>
                    </div>

                    <div class="mb-3">
                        <textarea class="form-control font-monospace" name="manual_ids" rows="10" required placeholder="Masukkan ID Wilkerstat di sini..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Layanan -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">2. Pilih Layanan</h6>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="need_download" value="1" id="chkDownload" checked>
                        <label class="form-check-label" for="chkDownload">
                            <strong>Unduhan (Download)</strong><br>
                            <span class="text-secondary small">Siapkan file JPG/RAR/ZIP.</span>
                        </label>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="need_print" value="1" id="chkPrint">
                        <label class="form-check-label" for="chkPrint">
                            <strong>Pencetakan (Print)</strong><br>
                            <span class="text-secondary small">Cetak peta fisik melalui printer kantor.</span>
                        </label>
                    </div>

                    <div id="printOptions" class="d-none bg-body-tertiary p-3 rounded border mb-4">
                        <label class="form-label small fw-semibold">Jumlah Salinan Cetak</label>
                        <input type="number" class="form-control" name="print_copies" value="1" min="1" max="10">
                    </div>

                    <h6 class="fw-bold mb-2 mt-4">3. Catatan (Opsional)</h6>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Contoh: Mohon dicetak dengan ukuran A3."></textarea>

                    <button type="submit" class="btn btn-primary w-100 mt-4">
                        <i class="bi bi-send me-1"></i> Kirim Permintaan
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
document.getElementById('chkPrint').addEventListener('change', function() {
    document.getElementById('printOptions').classList.toggle('d-none', !this.checked);
});
</script>