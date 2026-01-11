<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin exists to avoid duplicates
        if (!User::where('email', 'admin@alphv.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@alphv.com',
                'password' => Hash::make('Admin123@'),
            ]);
        }
    }
}