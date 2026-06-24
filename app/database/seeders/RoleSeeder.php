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
            'view calls',       // view calls (scoped to assigned ones by default)
            'view all calls',   // view every call, not just the assigned ones
            'manage calls',     // create/update calls
            'delete calls',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'admin'      => $permissions, // full access
            'manager'    => ['manage employees', 'view calls', 'view all calls', 'manage calls'],
            // Technicians only see the calls assigned to them (no "view all calls").
            'technician' => ['view calls', 'manage calls'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
