<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Support\Session;

class AuditService
{
    public static function log(string $action, string $entityType, ?int $entityId = null, ?array $old = null, ?array $new = null): void
    {
        $user = Session::get('_auth');
        AuditLog::write(
            $user['id'] ?? null,
            $action,
            $entityType,
            $entityId,
            $old,
            $new,
            client_ip(),
            user_agent()
        );
    }
}