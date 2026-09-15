<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin.');
        }

        // Login saja tidak cukup — area admin memuat data pribadi seluruh responden.
        if (!auth()->user()->is_admin) {
            abort(403, 'Anda tidak punya akses ke halaman admin.');
        }

        return $next($request);
    }
}