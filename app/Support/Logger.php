<?php

namespace App\Support;

class Logger
{
    private static function path(): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . '/app.log';
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $line = sprintf(
            "[%s] %s: %s%s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents(self::path(), $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $m, array $c = []): void  { self::log('info', $m, $c); }
    public static function error(string $m, array $c = []): void { self::log('error', $m, $c); }
    public static function debug(string $m, array $c = []): void { self::log('debug', $m, $c); }
}