<?php

namespace App\Middleware;

class CsrfMiddleware
{
    public function handle(): bool
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!verify_csrf($token)) {
            http_response_code(419);
            \App\Support\View::render('shared.403', ['reason' => 'CSRF token tidak valid.'], 'app');
            return false;
        }
        return true;
    }
}