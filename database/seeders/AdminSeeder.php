<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin SehatEdukasi',
            'email' => 'admin@sehatedukasi.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}