<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RespondentAuthController extends Controller
{
    /** Percobaan kode salah per IP per menit. Longgar, karena hanya kegagalan yang dihitung. */
    private const MAX_PERCOBAAN_LOGIN = 10;

    /** SEHAT0001 … SEHAT9999 */
    private const KODE_MIN = 1;
    private const KODE_MAX = 9999;

    public function showLogin()
    {
        if (session()->has('respondent_id')) {
            return redirect()->route('respondent.home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string',
        ], [
            'access_code.required' => 'Kode akses wajib diisi.',
        ]);

        // Login hanya bermodal kode akses, jadi percobaan tebak-tebakan harus dibatasi.
        // Yang dihitung cuma percobaan GAGAL — satu tempat praktik dengan banyak
        // responden yang mengetik kode benar dari satu WiFi tidak akan terblokir.
        $key = 'login-responden:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_PERCOBAAN_LOGIN)) {
            return back()->with('error', 'Terlalu banyak percobaan kode yang salah. Coba lagi dalam '
                . RateLimiter::availableIn($key) . ' detik.');
        }

        $respondent = Respondent::where('access_code', strtoupper($request->access_code))->first();

        if (!$respondent) {
            RateLimiter::hit($key, 60);
            return back()->with('error', 'Kode akses tidak ditemukan.');
        }

        RateLimiter::clear($key);

        // Ganti ID sesi saat login. Tanpa ini, cookie yang sudah dipegang orang
        // lain sebelum login ikut terautentikasi — nyata di perangkat bersama
        // di tempat praktik bidan.
        $request->session()->regenerate();

        session(['respondent_id' => $respondent->id]);
        return redirect()->route('respondent.home');
    }

    public function showRegister()
    {
        $locations = Location::orderBy('name')->get();
        return view('auth.register', compact('locations'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'age'            => 'required|integer|min:1|max:120',
            'gender'         => 'required|in:laki-laki,perempuan',
            'marital_status' => 'required|in:belum menikah,menikah,cerai hidup,cerai mati',
            'occupation'     => 'required|string|max:255',
            'total_children'  => 'required|integer|min:0|max:20',
            'medical_history' => 'nullable|array',
            'location_id'    => 'nullable|exists:locations,id',
        ]);

        $code = $this->generateAccessCode();

        if ($code === null) {
            return back()->withInput()->with('error',
                'Kuota kode akses sudah penuh. Hubungi admin untuk menambah kapasitas.');
        }

        $respondent = Respondent::create([
            'access_code'    => $code,
            'name'           => $request->name,
            'age'            => $request->age,
            'gender'         => $request->gender,
            'marital_status' => $request->marital_status,
            'occupation'     => $request->occupation,
            'total_children' => $request->total_children,
            'medical_history'=> $request->medical_history ?? [],
            'location_id'    => $request->location_id,
        ]);

        $request->session()->regenerate();
        session(['respondent_id' => $respondent->id]);

        return redirect()->route('respondent.home')->with('success', 'Pendaftaran berhasil! Kode akses kamu: ' . $code);
    }

    public function logout()
    {
        // Buang seluruh sesi, bukan cuma respondent_id: perangkat di tempat
        // praktik bidan dipakai bergantian, sisa sesi tidak boleh bisa dipakai lagi.
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Kode akses acak SEHAT0001–SEHAT9999.
     *
     * Acak, bukan berurutan, supaya kode tetangga tidak bisa ditebak dari kode
     * sendiri. Percobaannya dibatasi: versi lama memakai do…while tanpa batas,
     * sehingga saat kuota penuh request menggantung sampai PHP timeout alih-alih
     * memberi pesan. Mengembalikan null bila tidak ada kode tersisa.
     */
    private function generateAccessCode(): ?string
    {
        for ($i = 0; $i < 50; $i++) {
            $code = 'SEHAT' . str_pad(random_int(self::KODE_MIN, self::KODE_MAX), 4, '0', STR_PAD_LEFT);

            if (!Respondent::where('access_code', $code)->exists()) {
                return $code;
            }
        }

        // 50 tebakan acak meleset semua — kuota hampir penuh. Cari sisa yang ada
        // secara pasti, sekali query, daripada terus menebak.
        $terpakai = Respondent::pluck('access_code')->flip();

        for ($n = self::KODE_MIN; $n <= self::KODE_MAX; $n++) {
            $code = 'SEHAT' . str_pad($n, 4, '0', STR_PAD_LEFT);

            if (!$terpakai->has($code)) {
                return $code;
            }
        }

        return null;
    }
}