<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // DENY, bukan SAMEORIGIN: tidak ada satu pun halaman aplikasi ini yang
        // perlu di-iframe, jadi tutup rapat peluang clickjacking.
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'same-origin');

        // HSTS hanya di produksi — kalau dipasang di localhost, browser akan
        // memaksa https ke localhost dan sulit dibatalkan saat development.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
