<?php

namespace App\Models;

class AuditLog extends BaseModel
{
    protected static string $table = 'audit_logs';

    public static function write(
        ?int $actorId,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        $stmt = self::db()->prepare(
            'INSERT INTO audit_logs (actor_id, action, entity_type, entity_id, old_values, new_values, ip, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $actorId,
            $action,
            $entityType,
            $entityId,
            $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
            $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
            $ip,
            $userAgent,
        ]);
    }
}