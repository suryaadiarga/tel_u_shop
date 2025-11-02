<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $resp = $next($request);

        $csp = "default-src 'self' data: blob: https:;
                img-src 'self' data: https:;
                style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
                font-src 'self' https://fonts.gstatic.com;
                script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com;
                connect-src 'self';
                frame-ancestors 'none';";

        $resp->headers->set('X-Frame-Options', 'DENY');
        $resp->headers->set('X-Content-Type-Options', 'nosniff');
        $resp->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $resp->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $resp->headers->set('Content-Security-Policy', preg_replace('/\s+/', ' ', $csp));

        return $resp;
    }
}
