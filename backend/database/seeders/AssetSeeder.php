<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $electrical = AssetCategory::where('name', 'Electrical Equipment')->first();
        $mechanical = AssetCategory::where('name', 'Mechanical Equipment')->first();
        $hvac       = AssetCategory::where('name', 'HVAC')->first();

        $assets = [
            [
                'asset_category_id' => $electrical?->id,
                'name'         => 'Main Distribution Panel A',
                'code'         => 'ELEC-MDP-001',
                'description'  => 'Main electrical distribution panel for building A',
                'location'     => 'Building A, Floor 1, Room 101',
                'status'       => 'active',
                'purchased_at' => '2020-03-15',
            ],
            [
                'asset_category_id' => $mechanical?->id,
                'name'         => 'Centrifugal Pump P-201',
                'code'         => 'MECH-PMP-201',
                'description'  => 'Primary cooling water pump',
                'location'     => 'Pump House B',
                'status'       => 'active',
                'purchased_at' => '2019-07-20',
            ],
            [
                'asset_category_id' => $hvac?->id,
                'name'         => 'AHU Unit 1',
                'code'         => 'HVAC-AHU-001',
                'description'  => 'Air handling unit for floors 1-3',
                'location'     => 'Rooftop, Zone A',
                'status'       => 'active',
                'purchased_at' => '2021-01-10',
            ],
            [
                'asset_category_id' => $mechanical?->id,
                'name'         => 'Generator Set G-101',
                'code'         => 'MECH-GEN-101',
                'description'  => 'Emergency backup generator 500kVA',
                'location'     => 'Generator Room, Basement',
                'status'       => 'under_maintenance',
                'purchased_at' => '2018-11-05',
            ],
        ];

        foreach ($assets as $a) {
            Asset::firstOrCreate(['code' => $a['code']], $a);
        }
    }
}
