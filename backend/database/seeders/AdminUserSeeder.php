<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'      => 'Admin User',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $admin->assignRole('admin');

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'name'      => 'Supervisor User',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $supervisor->assignRole('supervisor');

        $inspector = User::firstOrCreate(
            ['email' => 'inspector@example.com'],
            [
                'name'      => 'Inspector User',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $inspector->assignRole('inspector');
    }
}
