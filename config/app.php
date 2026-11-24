<?php
return [
    'name'     => $_ENV['APP_NAME']     ?? 'SIMAP Wilkerstat',
    'env'      => $_ENV['APP_ENV']      ?? 'production',
    'debug'    => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'url'      => rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Jakarta',
    'locale'   => $_ENV['APP_LOCALE']   ?? 'id_ID',
    'key'      => $_ENV['APP_KEY']      ?? '',
];