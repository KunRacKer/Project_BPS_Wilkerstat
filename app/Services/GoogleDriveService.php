<?php

namespace App\Services;

use App\Support\Config;

class GoogleDriveService
{
    public static function isEnabled(): bool
    {
        return (bool)Config::get('google_drive.enabled');
    }

    public static function testConnection(): array
    {
        $cfg = Config::get('google_drive');
        
        if (!$cfg['enabled']) {
            return ['ok' => false, 'message' => 'Integrasi Google Drive dinonaktifkan di .env'];
        }

        if (empty($cfg['client_id']) || empty($cfg['client_secret']) || empty($cfg['refresh_token'])) {
            return ['ok' => false, 'message' => 'Kredensial Google Drive belum lengkap.'];
        }

        // TODO: Implementasi pengecekan HTTP ke Google API (FASE 5)
        return ['ok' => true, 'message' => 'Konfigurasi terdeteksi (Simulasi Sukses).'];
    }
}