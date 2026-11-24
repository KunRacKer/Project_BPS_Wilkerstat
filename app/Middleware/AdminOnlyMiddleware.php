<?php
namespace App\Middleware;
class AdminOnlyMiddleware extends RoleMiddleware
{
    protected function allowedRoles(): array { return ['super_admin']; }
}