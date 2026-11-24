<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Middleware\AdminOnlyMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\KetuaTimOnlyMiddleware;
use App\Controllers\UserController;

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