<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
{
    User::firstOrCreate(
        ['email' => 'admin@gmail.com'],
        [
            'name' => 'Admin SehatEdukasi',
            'password' => Hash::make('admin123'),
        ]
    );
}
}