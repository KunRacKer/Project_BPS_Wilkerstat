<?php

namespace App\Controllers;

use App\Services\RequestService;
use App\Support\Database;
use App\Support\Session;
use App\Support\View;

class VerificationController
{
    public function index(): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->query("
            SELECT r.*, p.full_name as requester_name
            FROM requests r
            JOIN users u ON u.id = r.requester_id
            LEFT JOIN user_profiles p ON p.user_id = u.id
            WHERE r.status IN ('waiting_verification', 'processing')
            ORDER BY r.submitted_at ASC
        ");
        $requests = $stmt->fetchAll();

        View::render('admin.verifications.index', [
            'title' => 'Antrean Verifikasi',
            'requests' => $requests
        ], 'app');
    }

    public function show(string $id): void
    {
        $pdo = Database::pdo();
        
        $stmt = $pdo->prepare("
            SELECT r.*, p.full_name as requester_name
            FROM requests r
            JOIN users u ON u.id = r.requester_id
            LEFT JOIN user_profiles p ON p.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $request = $stmt->fetch();
        
        if (!$request) {
            Session::flash('error', 'Permintaan tidak ditemukan.');
            redirect('admin/verifications');
        }

        $stmt = $pdo->prepare("
            SELECT ri.*, w.id_wilkerstat, w.nama_sls 
            FROM request_items ri
            JOIN wilkerstat w ON w.id = ri.wilkerstat_id
            WHERE ri.request_id = ?
        ");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll();

        View::render('admin.verifications.show', [
            'title' => 'Verifikasi Permintaan',
            'request' => $request,
            'items' => $items
        ], 'app');
    }

    public function process(string $id): void
    {
        $user = Session::get('_auth');
        $decisions = $_POST['items'] ?? []; // Array [item_id => ['status'=>..., 'reason'=>...]]
        
        if (empty($decisions)) {
            Session::flash('error', 'Tidak ada keputusan yang diubah.');
            redirect('admin/verifications/' . $id);
            return;
        }

        $result = RequestService::processVerification((int)$id, (int)$user['id'], $decisions);
        
        if ($result['ok']) {
            Session::flash('success', $result['message']);
            redirect('admin/verifications');
        } else {
            Session::flash('error', $result['message']);
            redirect('admin/verifications/' . $id);
        }
    }
}