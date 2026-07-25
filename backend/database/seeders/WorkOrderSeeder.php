<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email', 'admin@example.com')->first();
        $inspector = User::where('email', 'inspector@example.com')->first();
        $asset    = Asset::where('code', 'ELEC-MDP-001')->first();
        $asset2   = Asset::where('code', 'MECH-PMP-201')->first();

        if (!$admin || !$asset) {
            return;
        }

        WorkOrder::firstOrCreate(
            ['title' => 'Quarterly Electrical Inspection'],
            [
                'asset_id'     => $asset->id,
                'created_by'   => $admin->id,
                'assigned_to'  => $inspector?->id,
                'description'  => 'Perform quarterly inspection of main distribution panel',
                'status'       => 'pending',
                'priority'     => 'high',
                'latitude'     => 40.7128,
                'longitude'    => -74.0060,
                'scheduled_at' => now()->addDays(7),
            ]
        );

        if ($asset2) {
            WorkOrder::firstOrCreate(
                ['title' => 'Pump P-201 Monthly Check'],
                [
                    'asset_id'     => $asset2->id,
                    'created_by'   => $admin->id,
                    'assigned_to'  => $inspector?->id,
                    'description'  => 'Monthly condition monitoring of centrifugal pump',
                    'status'       => 'in_progress',
                    'priority'     => 'medium',
                    'latitude'     => 40.7580,
                    'longitude'    => -73.9855,
                    'scheduled_at' => now()->addDays(2),
                ]
            );
        }
    }
}
