<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view-dashboard',
            'view-inventory',
            'create-loan-application',
            'view-own-applications',
            'manage-districts',
            'manage-categories',
            'manage-items',
            'manage-storage-locations',
            'manage-users',
            'manage-loan-applications',
            'approve-loan-applications',
            'view-reports',
            'export-data',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create Roles & Assign Permissions
        $userRole = Role::create(['name' => 'user', 'guard_name' => 'web']);
        $userRole->givePermissionTo([
            'view-dashboard',
            'view-inventory',
            'create-loan-application',
            'view-own-applications',
        ]);

        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([
            'view-dashboard',
            'view-inventory',
            'manage-districts',
            'manage-categories',
            'manage-items',
            'manage-storage-locations',
            'manage-loan-applications',
            'approve-loan-applications',
            'view-reports',
            'export-data',
        ]);

        $superAdminRole = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
