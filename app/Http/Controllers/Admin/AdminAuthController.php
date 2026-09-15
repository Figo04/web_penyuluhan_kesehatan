<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthController extends Controller
{
    /** Percobaan login admin yang gagal, per email+IP per menit. */
    private const MAX_PERCOBAAN = 5;

    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Akun admin adalah satu-satunya pintu ke data seluruh responden, dan
        // alamatnya mudah ditebak. Tanpa batas ini, password bisa ditembak
        // tanpa henti. Kunci memakai email + IP, bukan IP saja, supaya satu
        // admin yang salah ketik tidak mengunci admin lain di jaringan sama.
        $key = 'login-admin:' . strtolower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_PERCOBAAN)) {
            Log::warning('Login admin diblokir rate limit', [
                'email' => $request->email,
                'ip'    => $request->ip(),
            ]);

            return back()->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam '
                . RateLimiter::availableIn($key) . ' detik.');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            Log::info('Login admin berhasil', [
                'email' => $request->email,
                'ip'    => $request->ip(),
            ]);

            return redirect()->route('admin.dashboard');
        }

        RateLimiter::hit($key, 60);

        Log::warning('Login admin gagal', [
            'email' => $request->email,
            'ip'    => $request->ip(),
        ]);

        return back()->with('error', 'Email atau password salah.');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
