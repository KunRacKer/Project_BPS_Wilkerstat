<?php

namespace App\Models;

use App\Support\Database;
use PDO;

abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    protected static function db(): PDO
    {
        return Database::pdo();
    }

    public static function find(int|string $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM `' . static::$table . '` WHERE `' . static::$primaryKey . '` = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(): array
    {
        return self::db()->query('SELECT * FROM `' . static::$table . '`')->fetchAll();
    }

    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($params);
    }
}