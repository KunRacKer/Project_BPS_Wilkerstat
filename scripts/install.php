<?php

/**
 * Installer CLI - membuat akun Super Admin pertama.
 * Penggunaan:
 *   php scripts/install.php
 *
 * Prasyarat:
 *   - .env sudah dikonfigurasi (DB & APP_KEY)
 *   - database/simap_wilkerstat.sql sudah diimpor
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Installer hanya boleh dijalankan dari CLI.\n");
    exit(1);
}

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

use App\Models\User;
use App\Support\Config;
use App\Support\Database;
use App\Support\Logger;

Config::load(BASE_PATH);

// Cek koneksi
try {
    $pdo = Database::pdo();
    $pdo->query('SELECT 1');
} catch (Throwable $e) {
    fwrite(STDERR, "Gagal terhubung ke database. Periksa .env.\n");
    exit(1);
}

// Cek apakah sudah ada super admin
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'super_admin'");
if ((int)$stmt->fetchColumn() > 0) {
    fwrite(STDERR, "Super Admin sudah ada. Instalasi dibatalkan untuk mencegah duplikasi.\n");
    exit(1);
}

echo "=== Instalasi SIMAP Wilkerstat - Akun Super Admin ===\n";

$username = trim((string)readline("Username Super Admin: "));
if ($username === '' || mb_strlen($username) < 3 || mb_strlen($username) > 50) {
    fwrite(STDERR, "Username harus 3-50 karakter.\n");
    exit(1);
}
if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
    fwrite(STDERR, "Username hanya boleh huruf, angka, garis bawah, dan titik.\n");
    exit(1);
}

$password = (string)readline("Password (min 8 karakter): ");
if (mb_strlen($password) < 8) {
    fwrite(STDERR, "Password minimal 8 karakter.\n");
    exit(1);
}
$confirm = (string)readline("Ulangi password: ");
if ($password !== $confirm) {
    fwrite(STDERR, "Password tidak sama.\n");
    exit(1);
}

$fullName = trim((string)readline("Nama lengkap: "));
if ($fullName === '') {
    fwrite(STDERR, "Nama lengkap wajib diisi.\n");
    exit(1);
}

$whatsapp = trim((string)readline("Nomor WhatsApp (opsional, format 62xxxx): "));

try {
    $pdo->beginTransaction();

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $userId = User::createUser($username, $hash, 'super_admin');
    User::createProfile($userId, $fullName, $whatsapp !== '' ? $whatsapp : null);

    $pdo->commit();

    Logger::info('Super Admin created via installer', ['user_id' => $userId, 'username' => $username]);
    echo "\n[OK] Super Admin berhasil dibuat. ID: $userId\n";
    echo "Silakan login melalui halaman /login.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    Logger::error('Installer failed', ['message' => $e->getMessage()]);
    fwrite(STDERR, "Gagal membuat Super Admin: " . $e->getMessage() . "\n");
    exit(1);
}