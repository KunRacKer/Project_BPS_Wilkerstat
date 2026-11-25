<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Middleware\AdminOnlyMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\KetuaTimOnlyMiddleware;
use App\Controllers\UserController;
use App\Controllers\RegionController;
use App\Controllers\RequestController;
use App\Controllers\VerificationController;
use App\Controllers\DownloadController;

/** @var \App\Support\Router $router */

// Auth
$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Admin
$router->get('/admin/dashboard', [DashboardController::class, 'admin'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);

// Ketua Tim
$router->get('/app/dashboard', [DashboardController::class, 'ketuaTim'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);

// Root redirect
$router->get('/', function () {
    $user = \App\Support\Session::get('_auth');
    if (!$user) {
        redirect('login');
        return;
    }
    redirect($user['role'] === 'super_admin' ? 'admin/dashboard' : 'app/dashboard');
});

$router->get('/admin/profile', [ProfileController::class, 'index'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->post('/admin/profile/update', [ProfileController::class, 'update'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/profile/password', [ProfileController::class, 'updatePassword'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);

$router->get('/app/profile', [ProfileController::class, 'index'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);
$router->post('/app/profile/update', [ProfileController::class, 'update'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class, CsrfMiddleware::class]);
$router->post('/app/profile/password', [ProfileController::class, 'updatePassword'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class, CsrfMiddleware::class]);

$router->get('/admin/users', [UserController::class, 'index'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->get('/admin/users/create', [UserController::class, 'create'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->post('/admin/users', [UserController::class, 'store'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/users/{id}/edit', [UserController::class, 'edit'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->post('/admin/users/{id}/edit', [UserController::class, 'update'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/users/{id}/toggle', [UserController::class, 'toggleStatus'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/users/{id}/reset-password', [UserController::class, 'resetPassword'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);

$router->get('/admin/regions', [RegionController::class, 'index'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->post('/admin/regions', [RegionController::class, 'store'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/regions/delete', [RegionController::class, 'delete'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);

// === KETUA TIM ===
$router->get('/app/requests', [RequestController::class, 'index'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);
$router->get('/app/requests/create', [RequestController::class, 'create'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);
$router->post('/app/requests', [RequestController::class, 'store'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class, CsrfMiddleware::class]);
$router->get('/app/requests/{id}', [RequestController::class, 'show'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);

// === SUPER ADMIN ===
$router->get('/admin/verifications', [VerificationController::class, 'index'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->get('/admin/verifications/{id}', [VerificationController::class, 'show'], [AuthMiddleware::class, AdminOnlyMiddleware::class]);
$router->post('/admin/verifications/{id}', [VerificationController::class, 'process'], [AuthMiddleware::class, AdminOnlyMiddleware::class, CsrfMiddleware::class]);

$router->get('/app/requests/{id}/downloads', [DownloadController::class, 'index'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);
$router->post('/app/requests/{id}/downloads', [DownloadController::class, 'store'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class, CsrfMiddleware::class]);
$router->get('/app/downloads/{id}/status', [DownloadController::class, 'status'], [AuthMiddleware::class, KetuaTimOnlyMiddleware::class]);