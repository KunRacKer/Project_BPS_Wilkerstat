<?php

namespace App\Services;

use App\Models\User;
use App\Support\Database;
use App\Support\Session;

class ProfileService
{
    public static function updateProfile(int $userId, array $data): array
    {
        $fullName = trim($data['full_name'] ?? '');
        $whatsapp = trim($data['whatsapp'] ?? '');

        if (empty($fullName)) {
            return ['ok' => false, 'message' => 'Nama lengkap wajib diisi.'];
        }

        try {
            $pdo = Database::pdo();
            $pdo->beginTransaction();

            // Update profile
            $stmt = $pdo->prepare('UPDATE user_profiles SET full_name = ?, whatsapp = ? WHERE user_id = ?');
            $stmt->execute([$fullName, $whatsapp !== '' ? $whatsapp : null, $userId]);

            // Perbarui session agar nama di topbar langsung berubah
            $auth = Session::get('_auth');
            $auth['full_name'] = $fullName;
            Session::set('_auth', $auth);

            $pdo->commit();

            AuditService::log('update_profile', 'users', $userId);

            return ['ok' => true, 'message' => 'Profil berhasil diperbarui.'];
        } catch (\Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['ok' => false, 'message' => 'Gagal memperbarui profil: ' . $e->getMessage()];
        }
    }

    public static function updatePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        if (mb_strlen($newPassword) < 8) {
            return ['ok' => false, 'message' => 'Password baru minimal 8 karakter.'];
        }

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['ok' => false, 'message' => 'Password saat ini salah.'];
        }

        try {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$newHash, $userId]);

            AuditService::log('update_password', 'users', $userId);

            return ['ok' => true, 'message' => 'Password berhasil diubah.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal mengubah password.'];
        }
    }
}