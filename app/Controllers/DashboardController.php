<?php

namespace App\Controllers;

use App\Models\DashboardStats;
use App\Support\Session;
use App\Support\View;

class DashboardController
{
    public function admin(): void
    {
        $stats = DashboardStats::getAdminStats();
        
        View::render('admin.dashboard', [
            'title' => 'Dashboard Super Admin',
            'stats' => $stats,
        ], 'app');
    }

    public function ketuaTim(): void
    {
        $user = Session::get('_auth');
        $stats = DashboardStats::getKetuaTimStats((int)$user['id']);
        
        View::render('ketua_tim.dashboard', [
            'title' => 'Dashboard Ketua Tim',
            'stats' => $stats,
        ], 'app');
    }
}