<?php

namespace App\Services;

use App\Models\User;
use App\Support\Database;

class UserService
{
    public static function createUser(array $data): array
    {
        $username = trim($data['username'] ?? '');
        $fullName = trim($data['full_name'] ?? '');
        $password = $data['password'] ?? '';
        $whatsapp = trim($data['whatsapp'] ?? '');

        // Validasi
        if (empty($username) || mb_strlen($username) < 3) {
            return ['ok' => false, 'message' => 'Username minimal 3 karakter.'];
        }
        if (empty($fullName)) {
            return ['ok' => false, 'message' => 'Nama lengkap wajib diisi.'];
        }
        if (mb_strlen($password) < 8) {
            return ['ok' => false, 'message' => 'Password minimal 8 karakter.'];
        }

        // Cek unique username
        $existing = User::findByUsername($username);
        if ($existing) {
            return ['ok' => false, 'message' => 'Username sudah digunakan.'];
        }

        try {
            $pdo = Database::pdo();
            $pdo->beginTransaction();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userId = User::createUser($username, $hash, 'ketua_tim');
            User::createProfile($userId, $fullName, $whatsapp !== '' ? $whatsapp : null);

            $pdo->commit();
            AuditService::log('create_user', 'users', $userId, null, ['username' => $username]);
            
            return ['ok' => true, 'message' => 'Pengguna berhasil ditambahkan.'];
        } catch (\Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
            return ['ok' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }
    }

    public static function updateUser(int $id, array $data): array
    {
        $username = trim($data['username'] ?? '');
        $fullName = trim($data['full_name'] ?? '');
        $whatsapp = trim($data['whatsapp'] ?? '');

        if (empty($username) || mb_strlen($username) < 3) {
            return ['ok' => false, 'message' => 'Username minimal 3 karakter.'];
        }
        if (empty($fullName)) {
            return ['ok' => false, 'message' => 'Nama lengkap wajib diisi.'];
        }

        // Cek unique username (kecuali milik sendiri)
        $existing = User::findByUsername($username);
        if ($existing && (int)$existing['id'] !== $id) {
            return ['ok' => false, 'message' => 'Username sudah digunakan pengguna lain.'];
        }

        try {
            User::updateUser($id, $username, $fullName, $whatsapp !== '' ? $whatsapp : null);
            AuditService::log('update_user', 'users', $id);
            return ['ok' => true, 'message' => 'Data pengguna berhasil diperbarui.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()];
        }
    }

    public static function resetPassword(int $id, string $newPassword): array
    {
        if (mb_strlen($newPassword) < 8) {
            return ['ok' => false, 'message' => 'Password minimal 8 karakter.'];
        }

        try {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = Database::pdo()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$hash, $id]);
            
            AuditService::log('reset_password_user', 'users', $id);
            return ['ok' => true, 'message' => 'Password pengguna berhasil direset.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal mereset password.'];
        }
    }
}