<?php

namespace Database\Seeders;

use App\Models\TechnicianLocation;
use App\Models\TechnicianProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = [
            [
                'name' => 'Electrician North',
                'email' => 'tech.north@example.com',
                'employee_code' => 'TECH-001',
                'phone' => '+15555550001',
                'default_area' => 'North Zone',
                'skills' => ['electrical', 'switchgear', 'outage'],
                'latitude' => 40.7580,
                'longitude' => -73.9855,
            ],
            [
                'name' => 'Electrician Central',
                'email' => 'tech.central@example.com',
                'employee_code' => 'TECH-002',
                'phone' => '+15555550002',
                'default_area' => 'Central Zone',
                'skills' => ['electrical', 'transformer', 'inspection'],
                'latitude' => 40.7128,
                'longitude' => -74.0060,
            ],
            [
                'name' => 'Electrician South',
                'email' => 'tech.south@example.com',
                'employee_code' => 'TECH-003',
                'phone' => '+15555550003',
                'default_area' => 'South Zone',
                'skills' => ['electrical', 'overhead-line', 'emergency'],
                'latitude' => 40.6892,
                'longitude' => -74.0445,
            ],
        ];

        foreach ($technicians as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            $user->assignRole('technician');

            TechnicianProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_code' => $data['employee_code'],
                    'skills' => $data['skills'],
                    'phone' => $data['phone'],
                    'default_area' => $data['default_area'],
                    'is_active' => true,
                ]
            );

            TechnicianLocation::create([
                'technician_id' => $user->id,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'accuracy' => 12.5,
                'captured_at' => now(),
            ]);
        }
    }
}
