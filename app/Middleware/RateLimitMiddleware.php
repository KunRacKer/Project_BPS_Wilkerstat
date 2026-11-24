<?php

namespace App\Middleware;

use App\Support\Session;

class RateLimitMiddleware
{
    // Slot default: 60 request / 60 detik
    public function __construct(private int $max = 60, private int $window = 60) {}

    public function handle(): bool
    {
        $now = time();
        $bucket = Session::get('_rl', ['start' => $now, 'count' => 0]);
        if ($now - $bucket['start'] > $this->window) {
            $bucket = ['start' => $now, 'count' => 0];
        }
        $bucket['count']++;
        Session::set('_rl', $bucket);
        if ($bucket['count'] > $this->max) {
            http_response_code(429);
            echo 'Too many requests.';
            return false;
        }
        return true;
    }
}