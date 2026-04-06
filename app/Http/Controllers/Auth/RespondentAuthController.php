<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RespondentAuthController extends Controller
{
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

        $respondent = Respondent::where('access_code', strtoupper($request->access_code))->first();

        if (!$respondent) {
            return back()->with('error', 'Kode akses tidak ditemukan.');
        }

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
            'medical_history'=> 'nullable|array',
            'location_id'    => 'nullable|exists:locations,id',
        ]);

        // Generate kode akses otomatis: SEHAT + 3 digit angka random
        do {
            $code = 'SEHAT' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Respondent::where('access_code', $code)->exists());

        $respondent = Respondent::create([
            'access_code'    => $code,
            'name'           => $request->name,
            'age'            => $request->age,
            'gender'         => $request->gender,
            'marital_status' => $request->marital_status,
            'occupation'     => $request->occupation,
            'medical_history'=> $request->medical_history ?? [],
            'location_id'    => $request->location_id,
        ]);

        session(['respondent_id' => $respondent->id]);
        return redirect()->route('respondent.home')->with('success', 'Pendaftaran berhasil! Kode akses kamu: ' . $code);
    }

    public function logout()
    {
        session()->forget('respondent_id');
        return redirect()->route('login');
    }
}