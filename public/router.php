<?php
/**
 * Router script untuk PHP built-in development server.
 * Penggunaan:
 *   php -S localhost:8000 -t public public/router.php
 *
 * File ini hanya untuk dev. Di Apache, .htaccess yang menangani rewrite.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$file = __DIR__ . $path;

// Layani file statis apa adanya (css, js, images, dsb.)
if ($path !== '/' && is_file($file)) {
    return false; // biarkan PHP server yang mengirim
}

// Selain itu, serahkan ke front controller
require __DIR__ . '/index.php';
