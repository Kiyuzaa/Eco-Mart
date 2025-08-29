<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin EcoMart',
            'email' => 'admin@ecomart.com',
            'password' => Hash::make('admin123'), // password asli: admin123
            'role' => 'admin',
        ]);

        // Customer
        User::create([
            'name' => 'Customer EcoMart',
            'email' => 'customer@ecomart.com',
            'password' => Hash::make('customer123'), // password asli: customer123
            'role' => 'customer',
        ]);
    }
}
