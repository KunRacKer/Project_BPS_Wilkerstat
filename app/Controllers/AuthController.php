<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Support\Session;
use App\Support\View;

class AuthController
{
    public function showLogin(): void
{
    if (Session::get('_auth')) {
        redirect(self::dashboardUrl());
        return;
    }
    $error = Session::pullFlash('error');
    $info  = Session::pullFlash('info');
    $warning = Session::pullFlash('warning');
    $oldUser = Session::pullFlash('old_username', '');
    View::render('auth.login', [
        'error'       => $error,
        'info'        => $info,
        'warning'     => $warning,
        'old_username'=> $oldUser,
    ], 'auth');
}

    public function login(): void
    {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $errors = validate(
            ['username' => $username, 'password' => $password],
            ['username' => 'required|max:50', 'password' => 'required|min:6'],
            ['username' => 'Username', 'password' => 'Password']
        );

        if ($errors) {
            Session::flash('error', reset($errors));
            Session::flash('old_username', $username);
            redirect('login');
            return;
        }

        $result = AuthService::attempt($username, $password);
        if (!$result['ok']) {
            Session::flash('error', $result['message']);
            Session::flash('old_username', $username);
            redirect('login');
            return;
        }

        redirect(self::dashboardUrl());
    }

    public function logout(): void
    {
        AuthService::logout();
        Session::flash('info', 'Anda telah keluar.');
        redirect('login');
    }

    public static function dashboardUrl(): string
    {
        $user = Session::get('_auth');
        if (!$user) {
            return 'login';
        }
        return $user['role'] === 'super_admin' ? 'admin/dashboard' : 'app/dashboard';
    }
}