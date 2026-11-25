<?php

namespace App\Services;

use App\Support\Database;

class RequestService
{
    public static function generateRequestNumber(): string
    {
        $pdo = Database::pdo();
        $year = date('Y');
        
        // Cari nomor terakhir di tahun ini
        $stmt = $pdo->prepare("SELECT request_number FROM requests WHERE request_number LIKE ? ORDER BY id DESC LIMIT 1 FOR UPDATE");
        $stmt->execute(["SIMAP-$year-%"]);
        $last = $stmt->fetchColumn();

        if ($last) {
            $seq = (int)substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return sprintf("SIMAP-%s-%04d", $year, $seq);
    }

    public static function createRequest(int $requesterId, array $data): array
    {
        $pdo = Database::pdo();
        
        $needDownload = isset($data['need_download']) && $data['need_download'] === '1';
        $needPrint = isset($data['need_print']) && $data['need_print'] === '1';
        
        if (!$needDownload && !$needPrint) {
            return ['ok' => false, 'message' => 'Pilih setidaknya satu layanan (Unduh atau Cetak).'];
        }

        $rawIds = $data['manual_ids'] ?? '';
        if (empty(trim($rawIds))) {
            return ['ok' => false, 'message' => 'Masukkan setidaknya satu ID Wilkerstat.'];
        }

        // Pisahkan berdasarkan enter, koma, atau spasi
        $ids = preg_split('/[\s,]+/', trim($rawIds));
        $ids = array_unique(array_filter($ids)); // Hapus duplikat & kosong

        try {
            $pdo->beginTransaction();

            // Validasi ID ke tabel master
            $validIds = [];
            $invalidIds = [];
            
            $stmt = $pdo->prepare("SELECT id FROM wilkerstat WHERE id_wilkerstat = ?");
            foreach ($ids as $id) {
                $stmt->execute([$id]);
                $wsId = $stmt->fetchColumn();
                if ($wsId) {
                    $validIds[$id] = $wsId;
                } else {
                    $invalidIds[] = $id;
                }
            }

            if (empty($validIds)) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Semua ID Wilkerstat tidak ditemukan di database master.'];
            }

            // Buat Header Request
            $reqNumber = self::generateRequestNumber();
            $stmt = $pdo->prepare("
                INSERT INTO requests (request_number, requester_id, status, need_download, need_print, print_copies, notes, submitted_at)
                VALUES (?, ?, 'waiting_verification', ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $reqNumber, $requesterId, $needDownload ? 1 : 0, $needPrint ? 1 : 0, 
                (int)($data['print_copies'] ?? 1), $data['notes'] ?? null
            ]);
            $requestId = (int)$pdo->lastInsertId();

            // Simpan Items
            $stmtItem = $pdo->prepare("INSERT INTO request_items (request_id, wilkerstat_id, source, status) VALUES (?, ?, 'manual', 'pending')");
            foreach ($validIds as $strId => $wsId) {
                $stmtItem->execute([$requestId, $wsId]);
            }

            // Buat Request Services
            if ($needDownload) {
                $pdo->prepare("INSERT INTO request_services (request_id, service_type, status) VALUES (?, 'download', 'pending')")->execute([$requestId]);
            }
            if ($needPrint) {
                $pdo->prepare("INSERT INTO request_services (request_id, service_type, status) VALUES (?, 'print', 'pending')")->execute([$requestId]);
            }

            $pdo->commit();
            AuditService::log('create_request', 'requests', $requestId, null, ['number' => $reqNumber]);

            $msg = 'Permintaan berhasil diajukan.';
            if (!empty($invalidIds)) {
                $msg .= ' Namun ' . count($invalidIds) . ' ID tidak ditemukan dan diabaikan.';
            }

            return ['ok' => true, 'message' => $msg, 'request_id' => $requestId];

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return ['ok' => false, 'message' => 'Gagal membuat permintaan: ' . $e->getMessage()];
        }
    }
    
    public static function processVerification(int $requestId, int $adminId, array $decisions): array
    {
        $pdo = Database::pdo();
        try {
            $pdo->beginTransaction();

            // Update setiap item
            $stmt = $pdo->prepare("UPDATE request_items SET status = ?, reject_reason = ?, verified_by = ?, verified_at = NOW() WHERE id = ? AND request_id = ?");
            foreach ($decisions as $itemId => $data) {
                $status = $data['status']; // approved, rejected, on_hold
                $reason = $data['reason'] ?? null;
                $stmt->execute([$status, $reason, $adminId, $itemId, $requestId]);
                
                // Catat riwayat approval
                $pdo->prepare("INSERT INTO request_approvals (request_item_id, decision, reason, decided_by) VALUES (?, ?, ?, ?)")
                    ->execute([$itemId, $status === 'approved' ? 'approve' : ($status === 'rejected' ? 'reject' : 'hold'), $reason, $adminId]);
            }

            // Hitung status agregat
            $stmt = $pdo->prepare("SELECT status, COUNT(*) as total FROM request_items WHERE request_id = ? GROUP BY status");
            $stmt->execute([$requestId]);
            $counts = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

            $totalItems = array_sum($counts);
            $approvedCount = $counts['approved'] ?? 0;
            $rejectedCount = $counts['rejected'] ?? 0;
            $pendingCount = $counts['pending'] ?? 0;
            $holdCount = $counts['on_hold'] ?? 0;

            $newStatus = 'processing'; // Default jika ada yang disetujui
            
            if ($approvedCount === 0) {
                if ($rejectedCount === $totalItems) {
                    $newStatus = 'rejected';
                } else if ($pendingCount > 0) {
                    $newStatus = 'waiting_verification'; // Belum selesai direview semua
                }
            }

            $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?")->execute([$newStatus, $requestId]);
            
            $pdo->commit();
            AuditService::log('verify_request', 'requests', $requestId);
            return ['ok' => true, 'message' => 'Verifikasi berhasil disimpan.'];

        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return ['ok' => false, 'message' => 'Gagal memproses verifikasi: ' . $e->getMessage()];
        }
    }
}