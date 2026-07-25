<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrical Equipment', 'description' => 'All electrical systems and components'],
            ['name' => 'Mechanical Equipment', 'description' => 'Pumps, compressors, and mechanical systems'],
            ['name' => 'HVAC', 'description' => 'Heating, ventilation, and air conditioning'],
            ['name' => 'Structural', 'description' => 'Buildings and structural components'],
            ['name' => 'Safety Equipment', 'description' => 'Fire suppression, alarms, and safety systems'],
        ];

        foreach ($categories as $cat) {
            AssetCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
