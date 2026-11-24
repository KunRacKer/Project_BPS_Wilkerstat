<?php

namespace App\Models;

use App\Support\Database;

class DashboardStats extends BaseModel
{
    public static function getAdminStats(): array
    {
        $pdo = Database::pdo();

        // Total pengguna aktif (Ketua Tim)
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'ketua_tim' AND is_active = 1");
        $totalKetuaTim = (int)$stmt->fetchColumn();

        // Total permintaan
        $stmt = $pdo->query("SELECT COUNT(*) FROM requests");
        $totalRequests = (int)$stmt->fetchColumn();

        // Permintaan menunggu verifikasi
        $stmt = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'waiting_verification'");
        $pendingVerifications = (int)$stmt->fetchColumn();

        // Antrean cetak
        $stmt = $pdo->query("SELECT COUNT(*) FROM print_jobs WHERE status IN ('queued', 'printing')");
        $printQueue = (int)$stmt->fetchColumn();

        // 5 Permintaan terbaru
        $stmt = $pdo->query("
            SELECT r.id, r.request_number, r.status, r.created_at, p.full_name AS requester_name
            FROM requests r
            JOIN users u ON u.id = r.requester_id
            LEFT JOIN user_profiles p ON p.user_id = u.id
            ORDER BY r.created_at DESC LIMIT 5
        ");
        $recentRequests = $stmt->fetchAll();

        return [
            'total_ketua_tim'      => $totalKetuaTim,
            'total_requests'       => $totalRequests,
            'pending_verifications'=> $pendingVerifications,
            'print_queue'          => $printQueue,
            'recent_requests'      => $recentRequests,
        ];
    }

    public static function getKetuaTimStats(int $userId): array
    {
        $pdo = Database::pdo();

        // Total permintaan user
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE requester_id = ?");
        $stmt->execute([$userId]);
        $totalRequests = (int)$stmt->fetchColumn();

        // Permintaan sedang diproses
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE requester_id = ? AND status IN ('waiting_verification', 'processing')");
        $stmt->execute([$userId]);
        $inProgress = (int)$stmt->fetchColumn();

        // Permintaan selesai
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE requester_id = ? AND status = 'completed'");
        $stmt->execute([$userId]);
        $completed = (int)$stmt->fetchColumn();

        // 5 permintaan terbaru
        $stmt = $pdo->prepare("
            SELECT id, request_number, status, created_at
            FROM requests
            WHERE requester_id = ?
            ORDER BY created_at DESC LIMIT 5
        ");
        $stmt->execute([$userId]);
        $recentRequests = $stmt->fetchAll();

        return [
            'total_requests'  => $totalRequests,
            'in_progress'     => $inProgress,
            'completed'       => $completed,
            'recent_requests' => $recentRequests,
        ];
    }
}