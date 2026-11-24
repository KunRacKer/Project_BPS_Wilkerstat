<?php

namespace App\Controllers;

use App\Services\RegionService;
use App\Support\Session;
use App\Support\View;

class RegionController
{
    // Daftar level yang valid (untuk mencegah manipulasi dari URL)
    private array $levels = ['provinsi', 'kabupaten', 'kecamatan', 'desa', 'sls'];

    public function index(): void
    {
        $currentId = isset($_GET['parent']) ? (int)$_GET['parent'] : null;
        
        // Ambil data wilayah saat ini (jika bukan root)
        $currentRegion = null;
        $breadcrumb = [];
        
        if ($currentId) {
            $breadcrumb = RegionService::getBreadcrumb($currentId);
            $currentRegion = end($breadcrumb);
        }

        $children = RegionService::getChildren($currentId);

        // Tentukan level selanjutnya
        $nextLevel = 'provinsi';
        if ($currentRegion) {
            $idx = array_search($currentRegion['level'], $this->levels);
            $nextLevel = $this->levels[$idx + 1] ?? null;
        }

        View::render('admin.regions.index', [
            'title' => 'Master Wilayah',
            'currentId' => $currentId,
            'currentRegion' => $currentRegion,
            'breadcrumb' => $breadcrumb,
            'children' => $children,
            'nextLevel' => $nextLevel
        ], 'app');
    }

    public function store(): void
    {
        $parentId = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
        $level = $_POST['level'] ?? '';
        $nama = $_POST['nama'] ?? '';
        $kode = $_POST['kode'] ?? '';

        if (!in_array($level, $this->levels)) {
            Session::flash('error', 'Level wilayah tidak valid.');
            redirect('admin/regions' . ($parentId ? '?parent=' . $parentId : ''));
            return;
        }

        $result = RegionService::create($nama, $parentId, $level, $kode);
        
        if ($result['ok']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }

        redirect('admin/regions' . ($parentId ? '?parent=' . $parentId : ''));
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        $result = RegionService::delete($id);
        
        if ($result['ok']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }

        redirect('admin/regions' . ($parentId ? '?parent=' . $parentId : ''));
    }
}