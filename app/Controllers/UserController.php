<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Support\Session;
use App\Support\View;

class UserController
{
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['q'] ?? '');
        
        $paginator = User::paginate($page, 10, $search);

        View::render('admin.users.index', [
            'title' => 'Manajemen Pengguna',
            'paginator' => $paginator,
            'search' => $search
        ], 'app');
    }

    public function create(): void
    {
        View::render('admin.users.create', ['title' => 'Tambah Pengguna'], 'app');
    }

    public function store(): void
    {
        $result = UserService::createUser($_POST);
        
        if ($result['ok']) {
            Session::flash('success', $result['message']);
            redirect('admin/users');
        } else {
            Session::flash('error', $result['message']);
            Session::flash('old', $_POST);
            redirect('admin/users/create');
        }
    }

    public function edit(string $id): void
    {
        $user = User::findWithProfile((int)$id);
        if (!$user) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            redirect('admin/users');
            return;
        }

        View::render('admin.users.edit', [
            'title' => 'Edit Pengguna',
            'user' => $user
        ], 'app');
    }

    public function update(string $id): void
    {
        $result = UserService::updateUser((int)$id, $_POST);
        
        if ($result['ok']) {
            Session::flash('success', $result['message']);
            redirect('admin/users');
        } else {
            Session::flash('error', $result['message']);
            redirect('admin/users/' . $id . '/edit');
        }
    }

    public function toggleStatus(string $id): void
    {
        $user = User::find((int)$id);
        if ($user) {
            $newStatus = !((bool)$user['is_active']);
            User::updateStatus((int)$id, $newStatus);
            Session::flash('success', 'Status pengguna berhasil diubah.');
        }
        redirect('admin/users');
    }

    public function resetPassword(string $id): void
    {
        $user = User::find((int)$id);
        if (!$user) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            redirect('admin/users');
            return;
        }

        // Generate password acak 8 karakter (alfanumerik)
        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        
        $result = UserService::resetPassword((int)$id, $newPassword);
        
        if ($result['ok']) {
            Session::flash('success', "Password berhasil direset menjadi: <strong>{$newPassword}</strong>. Mohon catat dan berikan ke pengguna.");
        } else {
            Session::flash('error', $result['message']);
        }
        
        redirect('admin/users');
    }
}