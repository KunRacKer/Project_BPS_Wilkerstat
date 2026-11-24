<?php

namespace App\Services;

use App\Support\Database;

class RegionService
{
    public static function getChildren(?int $parentId = null): array
    {
        $pdo = Database::pdo();
        if ($parentId === null) {
            // Ambil level teratas (Provinsi)
            $stmt = $pdo->query("SELECT * FROM regions WHERE parent_id IS NULL ORDER BY nama ASC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM regions WHERE parent_id = ? ORDER BY nama ASC");
            $stmt->execute([$parentId]);
        }
        return $stmt->fetchAll();
    }

    public static function getBreadcrumb(int $regionId): array
    {
        $pdo = Database::pdo();
        $breadcrumb = [];
        $currentId = $regionId;

        while ($currentId !== null) {
            $stmt = $pdo->prepare("SELECT id, parent_id, nama, level FROM regions WHERE id = ?");
            $stmt->execute([$currentId]);
            $row = $stmt->fetch();
            if (!$row) break;
            
            $breadcrumb[] = $row;
            $currentId = $row['parent_id'];
        }

        return array_reverse($breadcrumb);
    }

    public static function create(string $nama, ?int $parentId, string $level, string $kode): array
    {
        $nama = trim($nama);
        $kode = trim($kode);
        
        if (empty($nama) || empty($kode)) {
            return ['ok' => false, 'message' => 'Nama dan Kode wilayah wajib diisi.'];
        }

        try {
            $pdo = Database::pdo();
            
            // Cek kode unik
            $stmt = $pdo->prepare("SELECT id FROM regions WHERE kode = ?");
            $stmt->execute([$kode]);
            if ($stmt->fetch()) {
                return ['ok' => false, 'message' => 'Kode wilayah sudah digunakan.'];
            }

            $stmt = $pdo->prepare("INSERT INTO regions (parent_id, level, kode, nama) VALUES (?, ?, ?, ?)");
            $stmt->execute([$parentId, $level, $kode, $nama]);
            
            $id = $pdo->lastInsertId();
            AuditService::log('create_region', 'regions', (int)$id);
            
            return ['ok' => true, 'message' => 'Wilayah berhasil ditambahkan.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public static function delete(int $id): array
    {
        try {
            $pdo = Database::pdo();
            
            // Cek apakah punya anak
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM regions WHERE parent_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() > 0) {
                return ['ok' => false, 'message' => 'Tidak bisa dihapus. Wilayah ini memiliki sub-wilayah.'];
            }

            $stmt = $pdo->prepare("DELETE FROM regions WHERE id = ?");
            $stmt->execute([$id]);
            
            AuditService::log('delete_region', 'regions', $id);
            return ['ok' => true, 'message' => 'Wilayah berhasil dihapus.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal menghapus. Mungkin wilayah ini sedang digunakan.'];
        }
    }
}