<?php
namespace App\Middleware;
class KetuaTimOnlyMiddleware extends RoleMiddleware
{
    protected function allowedRoles(): array { return ['ketua_tim']; }
}