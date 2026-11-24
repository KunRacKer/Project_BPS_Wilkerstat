<?php

namespace App\Middleware;

use App\Support\Session;

abstract class RoleMiddleware
{
    abstract protected function allowedRoles(): array;

    public function handle(): bool
    {
        $user = Session::get('_auth');
        if (!$user || !in_array($user['role'], $this->allowedRoles(), true)) {
            http_response_code(403);
            \App\Support\View::render('shared.403', [], 'app');
            return false;
        }
        return true;
    }
}