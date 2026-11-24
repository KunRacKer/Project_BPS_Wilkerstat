<?php

declare(strict_types=1);

// --------------------------------------------
// Bootstrap SIMAP Wilkerstat
// --------------------------------------------
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

use App\Support\Config;
use App\Support\Logger;
use App\Support\Router;
use App\Support\Session;
use App\Support\View;

// Error handling: log everything, hanya tampilkan detail saat debug
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

try {
    Config::load(BASE_PATH);

    $appCfg = Config::get('app');
    date_default_timezone_set($appCfg['timezone'] ?? 'Asia/Jakarta');

    if (!empty($appCfg['debug'])) {
        ini_set('display_errors', '1');
    }

    // Storage dirs
    foreach (['logs', 'cache', 'temp', 'backups', 'generated'] as $dir) {
        $path = BASE_PATH . '/storage/' . $dir;
        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }
    }

    Session::start();

    $router = new Router();

    // Load routes
    require BASE_PATH . '/routes/web.php';

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri    = $_SERVER['REQUEST_URI'] ?? '/';

    $router->dispatch($method, $uri);

} catch (Throwable $e) {
    Logger::error('Unhandled exception: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    http_response_code(500);
    try {
        View::render('shared.500', [], 'app');
    } catch (Throwable $inner) {
        echo 'Terjadi kesalahan pada server.';
    }
}