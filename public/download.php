<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Support\Config;
use App\Support\Database;

Config::load(dirname(__DIR__));

$token = $_GET['token'] ?? '';
if (empty($token)) { die('Token tidak valid.'); }

$tokenHash = hash('sha256', $token);
$pdo = Database::pdo();

$sql = "SELECT d.*, req.request_number 
        FROM downloads d
        JOIN requests req ON req.id = d.request_id
        WHERE d.token_hash = ? 
          AND d.status = 'ready' 
          AND d.expires_at > NOW() 
          AND d.download_count < d.max_downloads";
$stmt = $pdo->prepare($sql);
$stmt->execute([$tokenHash]);
$download = $stmt->fetch();

if (!$download) {
    http_response_code(403);
    die('Tautan tidak valid, sudah kedaluwarsa, atau batas unduhan tercapai.');
}

$filePath = dirname(__DIR__) . '/storage/temp/' . $download['file_path'];
if (!is_file($filePath)) {
    http_response_code(404);
    die('File tidak ditemukan di server.');
}

$pdo->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?")->execute([$download['id']]);

if (ob_get_level()) ob_end_clean();
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $download['request_number'] . '.zip"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;