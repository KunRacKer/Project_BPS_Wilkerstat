<?php

namespace App\Middleware;

use App\Support\Session;

class AuthMiddleware
{
    public function handle(): bool
    {
        if (!Session::get('_auth')) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return false;
        }
        return true;
    }
}