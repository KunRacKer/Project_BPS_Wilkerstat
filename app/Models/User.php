<?php

namespace App\Models;

class User extends BaseModel
{
    protected static string $table = 'users';

    public static function findByUsername(string $username): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT u.*, p.full_name, p.whatsapp
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.id
             WHERE u.username = ? LIMIT 1'
        );
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findWithProfile(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT u.*, p.full_name, p.whatsapp, p.photo_path, p.jabatan
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.id
             WHERE u.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateLastLogin(int $id): void
    {
        $stmt = self::db()->prepare('UPDATE users SET last_login_at = NOW(), failed_attempts = 0, locked_until = NULL WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function recordFailedAttempt(int $id, int $newAttempts, ?string $lockedUntil): void
    {
        $stmt = self::db()->prepare('UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?');
        $stmt->execute([$newAttempts, $lockedUntil, $id]);
    }

    public static function createUser(string $username, string $passwordHash, string $role, ?int $createdBy = null): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO users (username, password_hash, role, created_by) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$username, $passwordHash, $role, $createdBy]);
        return (int)self::db()->lastInsertId();
    }

    public static function createProfile(int $userId, string $fullName, ?string $whatsapp = null, ?string $jabatan = null): void
    {
        $stmt = self::db()->prepare(
            'INSERT INTO user_profiles (user_id, full_name, whatsapp, jabatan) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $fullName, $whatsapp, $jabatan]);
    }

    public static function paginate(int $page = 1, int $perPage = 10, string $search = ''): array
    {
        $pdo = self::db();
        $offset = ($page - 1) * $perPage;
        
        $where = "u.role = 'ketua_tim'";
        $params = [];
        
        if ($search !== '') {
            $where .= " AND (u.username LIKE ? OR p.full_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        // Hitung total
        $countSql = "SELECT COUNT(*) FROM users u LEFT JOIN user_profiles p ON p.user_id = u.id WHERE $where";
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // Ambil data
        $sql = "SELECT u.*, p.full_name, p.whatsapp 
                FROM users u 
                LEFT JOIN user_profiles p ON p.user_id = u.id 
                WHERE $where 
                ORDER BY u.created_at DESC 
                LIMIT $perPage OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public static function updateStatus(int $id, bool $isActive): void
    {
        $stmt = self::db()->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->execute([$isActive ? 1 : 0, $id]);
    }

    public static function updateUser(int $id, string $username, string $fullName, ?string $whatsapp): void
    {
        $pdo = self::db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('UPDATE users SET username = ? WHERE id = ?');
            $stmt->execute([$username, $id]);

            $stmt = $pdo->prepare('UPDATE user_profiles SET full_name = ?, whatsapp = ? WHERE user_id = ?');
            $stmt->execute([$fullName, $whatsapp, $id]);
            
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}