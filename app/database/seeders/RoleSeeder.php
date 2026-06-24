<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Create the application roles and permissions (idempotent).
     */
    public function run(): void
    {
        $permissions = [
            'manage employees', // create/update/delete employees
            'view calls',
            'manage calls',     // create/update calls
            'delete calls',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'admin'      => $permissions, // full access
            'manager'    => ['manage employees', 'view calls', 'manage calls'],
            'technician' => ['view calls', 'manage calls'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
