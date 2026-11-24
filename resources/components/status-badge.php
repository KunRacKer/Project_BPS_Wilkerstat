<?php
/**
 * Pemakaian: View::partial('components.status-badge', ['status' => 'draft'])
 */
$status = $status ?? 'draft';
$map = [
    'draft'                => ['secondary', 'Draft'],
    'waiting_verification' => ['warning',   'Menunggu Verifikasi'],
    'processing'           => ['info',      'Sedang Diproses'],
    'completed'            => ['success',   'Selesai'],
    'rejected'             => ['danger',    'Ditolak'],
    'cancelled'            => ['dark',      'Dibatalkan'],
    'pending'              => ['secondary', 'Pending'],
    'approved'             => ['success',   'Disetujui'],
    'on_hold'              => ['warning',   'Ditunda'],
    'preparing'            => ['info',      'Menyiapkan'],
    'ready'                => ['success',   'Siap'],
    'expired'              => ['dark',      'Kedaluwarsa'],
    'failed'               => ['danger',    'Gagal'],
    'queued'               => ['secondary', 'Dalam Antrean'],
    'printing'             => ['info',      'Mencetak'],
    'done'                 => ['success',   'Selesai'],
];
[$color, $label] = $map[$status] ?? ['secondary', ucfirst($status)];
?>
<span class="badge text-bg-<?= e($color) ?> status-badge"><?= e($label) ?></span>