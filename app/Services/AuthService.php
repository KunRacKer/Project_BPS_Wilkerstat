<?php

namespace App\Services;

use App\Models\User;
use App\Support\Config;
use App\Support\Database;
use App\Support\Session;

class AuthService
{
    public static function attempt(string $username, string $password): array
    {
        $security = Config::get('security');
        $pdo = Database::pdo();

        // Rate limit by username+IP via login_attempts
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE username = ? AND ip_address = ? AND success = 0
               AND attempted_at > (NOW() - INTERVAL ? SECOND)'
        );
        $stmt->execute([$username, client_ip(), (int)$security['login_lock_seconds']]);
        if ((int)$stmt->fetchColumn() >= (int)$security['login_max_attempts']) {
            return ['ok' => false, 'message' => 'Terlalu banyak percobaan. Coba lagi beberapa saat.'];
        }

        $user = User::findByUsername($username);

        $ok = false;
        if ($user && (int)$user['is_active'] === 1) {
            if ($user['locked_until'] !== null && strtotime($user['locked_until']) > time()) {
                self::recordAttempt($username, false);
                return ['ok' => false, 'message' => 'Akun terkunci sementara. Hubungi Super Admin.'];
            }
            $ok = password_verify($password, $user['password_hash']);
        }

        self::recordAttempt($username, $ok);

        if (!$ok) {
            return ['ok' => false, 'message' => 'Username atau password salah.'];
        }

        // Rehash if needed
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $upd->execute([$newHash, $user['id']]);
        }

        // Persist session
        Session::regenerate();
        Session::set('_auth', [
            'id'        => (int)$user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'full_name' => $user['full_name'] ?? $user['username'],
        ]);

        User::updateLastLogin((int)$user['id']);

        // Login history
        $stmt = $pdo->prepare(
            'INSERT INTO login_histories (user_id, ip_address, user_agent, session_id) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([(int)$user['id'], client_ip(), user_agent(), session_id()]);

        AuditService::log('login', 'users', (int)$user['id']);

        return ['ok' => true, 'user' => $user];
    }

    public static function logout(): void
    {
        $user = Session::get('_auth');
        if ($user) {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare(
                'UPDATE login_histories SET logout_at = NOW()
                 WHERE user_id = ? AND session_id = ? AND logout_at IS NULL'
            );
            $stmt->execute([(int)$user['id'], session_id()]);

            AuditService::log('logout', 'users', (int)$user['id']);
        }
        Session::destroy();
    }

    private static function recordAttempt(string $username, bool $success): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, client_ip(), $success ? 1 : 0]);
    }
}