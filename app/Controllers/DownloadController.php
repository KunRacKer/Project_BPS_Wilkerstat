<?php

namespace App\Controllers;

use App\Services\DownloadService;
use App\Support\Database;
use App\Support\Session;
use App\Support\View;

class DownloadController
{
    public function index(string $requestId): void
    {
        $user = Session::get('_auth');
        $pdo = Database::pdo();

        // Ambil data request
        $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ? AND requester_id = ?");
        $stmt->execute([$requestId, $user['id']]);
        $request = $stmt->fetch();

        if (!$request) {
            Session::flash('error', 'Permintaan tidak ditemukan.');
            redirect('app/requests');
        }

        // Ambil riwayat download
        $stmt = $pdo->prepare("
            SELECT * FROM downloads 
            WHERE request_id = ? AND created_by = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$requestId, $user['id']]);
        $downloads = $stmt->fetchAll();

        View::render('ketua_tim.requests.downloads', [
            'title' => 'Unduhan File',
            'request' => $request,
            'downloads' => $downloads,
            'csrf_token' => csrf_token()
        ], 'app');
    }

    public function store(string $requestId): void
    {
        $user = Session::get('_auth');
        $type = $_POST['type'] ?? 'zip'; // jpg, rar, zip

        $result = DownloadService::createDownload((int)$requestId, (int)$user['id'], $type);

        if ($result['ok']) {
            Session::flash('success', $result['message']);
            if ($result['ready']) {
                // Jika sudah ready, langsung arahkan ke link download (kita perlu mengambil tokennya)
                // Karena token asli tidak disimpan (hanya hash), kita buatkan kembali (simulasi).
                // Di produksi, sebaiknya token disimpan di session atau di DB terenkripsi.
                $pdo = Database::pdo();
                $stmt = $pdo->prepare("SELECT token_hash FROM downloads WHERE id = ?");
                $stmt->execute([$result['download_id']]);
                // Untuk simplifikasi demo, kita reload halaman dan tampilkan tombol.
            }
            redirect('app/requests/' . $requestId . '/downloads');
        } else {
            Session::flash('error', $result['message']);
            redirect('app/requests/' . $requestId);
        }
    }

    public function status(string $id): void
    {
        $user = Session::get('_auth');
        $download = DownloadService::getDownloadStatus((int)$id, (int)$user['id']);
        
        if (!$download) {
            json_response(['status' => 'not_found'], 404);
        }

        json_response([
            'status' => $download['status'],
            'download_count' => $download['download_count'],
            'file_size' => $download['file_size']
        ]);
    }
}