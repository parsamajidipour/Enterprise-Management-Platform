<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'assets.view',
            'assets.create',
            'assets.edit',
            'assets.delete',
            'workorders.view',
            'workorders.create',
            'workorders.edit',
            'workorders.delete',
            'inspection-forms.view',
            'inspection-forms.create',
            'inspection-forms.edit',
            'inspection-forms.delete',
            'inspection-records.view',
            'inspection-records.create',
            'inspection-records.edit',
            'evidence.upload',
            'technician.view',
            'technician.manage',
            'dispatch.view',
            'dispatch.assign',
            'dispatch.reassign',
            'dispatch.cancel',
            'dispatch.recommend',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permissions);

        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'dashboard.view',
            'assets.view', 'assets.create', 'assets.edit',
            'workorders.view', 'workorders.create', 'workorders.edit', 'workorders.delete',
            'inspection-forms.view', 'inspection-forms.create', 'inspection-forms.edit',
            'inspection-records.view', 'inspection-records.create', 'inspection-records.edit',
            'evidence.upload',
        ]);

        $inspector = Role::firstOrCreate(['name' => 'inspector', 'guard_name' => 'web']);
        $inspector->syncPermissions([
            'dashboard.view',
            'assets.view',
            'workorders.view',
            'inspection-forms.view',
            'inspection-records.view', 'inspection-records.create',
            'evidence.upload',
        ]);

        $technician = Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'web']);
        $technician->syncPermissions([
            'workorders.view',
            'inspection-records.view', 'inspection-records.create',
            'evidence.upload',
        ]);

        $assetManager = Role::firstOrCreate(['name' => 'asset_manager', 'guard_name' => 'web']);
        $assetManager->syncPermissions([
            'dashboard.view',
            'assets.view', 'assets.create', 'assets.edit', 'assets.delete',
            'workorders.view', 'workorders.create', 'workorders.edit',
            'inspection-forms.view',
            'inspection-records.view',
        ]);
    }
}
