<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@mbg.com'],
            [
                'name' => 'Super Admin MBG',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Create or update Admin
        User::updateOrCreate(
            ['email' => 'admin@mbg.com'],
            [
                'name' => 'Admin Dapur MBG',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create or update Staf
        User::updateOrCreate(
            ['email' => 'staf@mbg.com'],
            [
                'name' => 'Staf Dapur MBG',
                'password' => Hash::make('password123'),
                'role' => 'staf',
                'is_active' => true,
            ]
        );

        // Create or update additional staf
        User::updateOrCreate(
            ['email' => 'ahmad@mbg.com'],
            [
                'name' => 'Ahmad Kurniawan',
                'password' => Hash::make('password123'),
                'role' => 'staf',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'siti@mbg.com'],
            [
                'name' => 'Siti Rahayu',
                'password' => Hash::make('password123'),
                'role' => 'staf',
                'is_active' => true,
            ]
        );
    }
}
