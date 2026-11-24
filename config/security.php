<?php
return [
    'session_name'       => $_ENV['SESSION_NAME']       ?? 'SIMAP_SESSID',
    'session_lifetime'   => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
    'session_secure'     => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOL),
    'session_samesite'   => $_ENV['SESSION_SAMESITE']   ?? 'Lax',
    'login_max_attempts' => (int)($_ENV['LOGIN_MAX_ATTEMPTS'] ?? 5),
    'login_lock_seconds' => (int)($_ENV['LOGIN_LOCK_SECONDS'] ?? 900),
];