<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\ProfileService;
use App\Support\Session;
use App\Support\View;

class ProfileController
{
    public function index(): void
    {
        $userAuth = Session::get('_auth');
        $user = User::findWithProfile((int)$userAuth['id']);

        View::render('shared.profile', [
            'title' => 'Profil Saya',
            'user' => $user,
            'isAdmin' => $userAuth['role'] === 'super_admin'
        ], 'app');
    }

    public function update(): void
    {
        $userAuth = Session::get('_auth');
        $result = ProfileService::updateProfile((int)$userAuth['id'], $_POST);

        if ($result['ok']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }

        $redirect = $userAuth['role'] === 'super_admin' ? 'admin/profile' : 'app/profile';
        redirect($redirect);
    }

    public function updatePassword(): void
    {
        $userAuth = Session::get('_auth');
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';

        $result = ProfileService::updatePassword((int)$userAuth['id'], $current, $new);

        if ($result['ok']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }

        $redirect = $userAuth['role'] === 'super_admin' ? 'admin/profile' : 'app/profile';
        redirect($redirect);
    }
}