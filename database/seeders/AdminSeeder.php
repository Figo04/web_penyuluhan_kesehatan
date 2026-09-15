<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Kredensial admin diambil dari environment supaya password produksi tidak
        // ikut tersimpan di repositori. Set ADMIN_EMAIL & ADMIN_PASSWORD di server.
        $password = env('ADMIN_PASSWORD', 'admin123');

        // Berhenti daripada diam-diam menyalakan server produksi dengan password
        // yang tertulis di repositori. Perintah seeder gagal dengan pesan ini,
        // sehingga pemasangan tidak bisa lanjut tanpa menyetel ADMIN_PASSWORD.
        if (app()->environment('production') && $password === 'admin123') {
            throw new \RuntimeException(
                'ADMIN_PASSWORD belum diset. Set environment variable ADMIN_PASSWORD '
                . 'di server sebelum deploy — password default tidak boleh dipakai di produksi.'
            );
        }

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@gmail.com')],
            [
                'name'     => env('ADMIN_NAME', 'Admin SehatEdukasi'),
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );
    }
}
