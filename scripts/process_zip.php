<?php
// scripts/process_zip.php

if (PHP_SAPI !== 'cli') {
    exit("Hanya bisa dijalankan via CLI.\n");
}

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

use App\Support\Config;
use App\Support\Database;
use App\Support\Logger;

Config::load(BASE_PATH);

$downloadId = (int)($argv[1] ?? 0);
$type = $argv[2] ?? 'zip'; // jpg, rar, zip

if (!$downloadId) {
    exit("Download ID tidak valid.\n");
}

try {
    $pdo = Database::pdo();
    
    // Ambil info download & items
    $stmt = $pdo->prepare("SELECT * FROM downloads WHERE id = ?");
    $stmt->execute([$downloadId]);
    $download = $stmt->fetch();
    
    if (!$download) exit("Download tidak ditemukan.\n");

    $stmt = $pdo->prepare("
        SELECT mf.file_type, w.id_wilkerstat 
        FROM download_items di
        JOIN map_files mf ON mf.id = di.map_file_id
        JOIN wilkerstat w ON w.id = mf.wilkerstat_id
        WHERE di.download_id = ?
    ");
    $stmt->execute([$downloadId]);
    $files = $stmt->fetchAll();

    if (empty($files)) {
        throw new Exception("Tidak ada file untuk diunduh.");
    }

    $tempDir = BASE_PATH . '/storage/temp/';
    $fileName = 'SIMAP_' . $download['request_id'] . '_' . $downloadId . '_' . time() . '.zip';
    $zipPath = $tempDir . $fileName;

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new Exception("Gagal membuat file ZIP.");
    }

    // SIMULASI: Karena belum ada Google Drive fisik, kita buat file dummy teks di dalam ZIP
    foreach ($files as $file) {
        $content = "Ini adalah simulasi file peta untuk ID Wilkerstat: " . $file['id_wilkerstat'] . "\nTipe file asli: " . strtoupper($file['file_type']);
        $zip->addFromString($file['id_wilkerstat'] . '_peta.' . $file['file_type'], $content);
    }
    
    // Jika tipenya ZIP, kita bungkus semua file tadi ke dalam ZIP. 
    // (Karena file asli belum ada, kita langsung simpan sebagai ZIP yang siap).
    $zip->close();

    // Update status di DB
    $size = filesize($zipPath);
    $stmt = $pdo->prepare("UPDATE downloads SET status = 'ready', file_path = ?, file_size = ?, ready_at = NOW() WHERE id = ?");
    $stmt->execute([$fileName, $size, $downloadId]);

    Logger::info("Download ZIP ready: $fileName");

} catch (Throwable $e) {
    Logger::error("Gagal memproses ZIP untuk ID $downloadId: " . $e->getMessage());
    $pdo->prepare("UPDATE downloads SET status = 'failed' WHERE id = ?")->execute([$downloadId]);
}