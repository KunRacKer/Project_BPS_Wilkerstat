<?php

namespace App\Services;

use App\Support\Database;
use App\Support\Logger;

class DownloadService
{
    public static function createDownload(int $requestId, int $userId, string $type): array
    {
        $pdo = Database::pdo();

        $stmt = $pdo->prepare("SELECT id FROM requests WHERE id = ? AND requester_id = ? AND status IN ('processing', 'completed')");
        $stmt->execute([$requestId, $userId]);
        if (!$stmt->fetch()) {
            return ['ok' => false, 'message' => 'Permintaan tidak valid atau belum disetujui.'];
        }

        // Cek existing (optional, agar tidak dobel)
        $stmt = $pdo->prepare("SELECT id FROM downloads WHERE request_id = ? AND download_type = ? AND status IN ('preparing', 'ready') ORDER BY id DESC LIMIT 1");
        $stmt->execute([$requestId, $type]);
        if ($existing = $stmt->fetch()) {
            return ['ok' => true, 'message' => 'Paket unduhan sudah ada.', 'download_id' => $existing['id'], 'ready' => false];
        }

        // Buat record baru
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $pdo->prepare("
            INSERT INTO downloads (request_id, download_type, status, token_hash, token_plain, expires_at, created_by)
            VALUES (?, ?, 'preparing', ?, ?, ?, ?)
        ");
        $stmt->execute([$requestId, $type, $tokenHash, $token, $expiresAt, $userId]);
        $downloadId = (int)$pdo->lastInsertId();

        // Simpan detail item (Query diperbaiki)
        $stmt = $pdo->prepare("
            SELECT ri.id as request_item_id, mf.id as map_file_id
            FROM request_items ri
            JOIN wilkerstat w ON w.id = ri.wilkerstat_id
            JOIN map_files mf ON mf.wilkerstat_id = w.id AND mf.status = 'active'
            WHERE ri.request_id = ? AND ri.status = 'approved'
        ");
        $stmt->execute([$requestId]);
        $files = $stmt->fetchAll();

        if (empty($files)) {
            $pdo->prepare("DELETE FROM downloads WHERE id = ?")->execute([$downloadId]);
            return ['ok' => false, 'message' => 'Tidak ada file peta yang tersedia untuk ID yang disetujui.'];
        }

        $stmtItem = $pdo->prepare("INSERT INTO download_items (download_id, request_item_id, map_file_id, included) VALUES (?, ?, ?, 1)");
        foreach ($files as $file) {
            $stmtItem->execute([$downloadId, $file['request_item_id'], $file['map_file_id']]);
        }

        // Jalankan background script
        $cmd = 'php ' . escapeshellarg(dirname(__DIR__, 2) . '/scripts/process_zip.php') . ' ' . $downloadId . ' ' . escapeshellarg($type);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen('start /B ' . $cmd, 'r'));
        } else {
            exec($cmd . ' > /dev/null 2>&1 &');
        }

        AuditService::log('request_download', 'downloads', $downloadId);
        return ['ok' => true, 'message' => 'File sedang disiapkan.', 'download_id' => $downloadId];
    }

    public static function getDownloadStatus(int $downloadId, int $userId): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("
            SELECT d.*, r.request_number 
            FROM downloads d
            JOIN requests r ON r.id = d.request_id
            WHERE d.id = ? AND d.created_by = ?
        ");
        $stmt->execute([$downloadId, $userId]);
        return $stmt->fetch() ?: null;
    }
}