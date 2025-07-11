<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin', // Menetapkan peran sebagai admin
            'password' => Hash::make('password'), // password default: password
        ]);
         User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'role' => 'user', // Peran default adalah user
            'password' => Hash::make('password'), // password default: password
        ]);
    }
}
