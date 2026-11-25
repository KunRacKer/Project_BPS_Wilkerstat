<?php

namespace App\Controllers;

use App\Services\RequestService;
use App\Support\Database;
use App\Support\Session;
use App\Support\View;

class RequestController
{
    public function index(): void
    {
        $user = Session::get('_auth');
        $pdo = Database::pdo();
        
        $stmt = $pdo->prepare("
            SELECT r.*, 
                   (SELECT COUNT(*) FROM request_items WHERE request_id = r.id) as total_items,
                   (SELECT COUNT(*) FROM request_items WHERE request_id = r.id AND status = 'approved') as approved_items
            FROM requests r 
            WHERE r.requester_id = ? 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$user['id']]);
        $requests = $stmt->fetchAll();

        View::render('ketua_tim.requests.index', [
            'title' => 'Permintaan Saya',
            'requests' => $requests
        ], 'app');
    }

    public function create(): void
    {
        View::render('ketua_tim.requests.create', ['title' => 'Ajukan Permintaan Baru'], 'app');
    }

    public function store(): void
    {
        $user = Session::get('_auth');
        $result = RequestService::createRequest((int)$user['id'], $_POST);

        if ($result['ok']) {
            Session::flash('success', $result['message']);
            redirect('app/requests/' . $result['request_id']);
        } else {
            Session::flash('error', $result['message']);
            redirect('app/requests/create');
        }
    }

    public function show(string $id): void
    {
        $user = Session::get('_auth');
        $pdo = Database::pdo();
        
        $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ? AND requester_id = ?");
        $stmt->execute([$id, $user['id']]);
        $request = $stmt->fetch();
        
        if (!$request) {
            Session::flash('error', 'Permintaan tidak ditemukan.');
            redirect('app/requests');
        }

        // Ambil item beserta nama SLS-nya
        $stmt = $pdo->prepare("
            SELECT ri.*, w.id_wilkerstat, w.nama_sls 
            FROM request_items ri
            JOIN wilkerstat w ON w.id = ri.wilkerstat_id
            WHERE ri.request_id = ?
        ");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll();

        View::render('ketua_tim.requests.show', [
            'title' => 'Detail Permintaan',
            'request' => $request,
            'items' => $items
        ], 'app');
    }
}