<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Create the default access accounts (idempotent).
     *  - admin:      test@example.com / password
     *  - technician: joao@empresa.com / password (linked to João's employee)
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => 'password', // hashed automatically by the model cast
            ]
        );

        $admin->syncRoles('admin');

        // Technician account linked to an existing employee, so we can demo the
        // "technician sees only their assigned calls" behaviour.
        $technician = User::firstOrCreate(
            ['email' => 'joao@empresa.com'],
            [
                'name'     => 'João Silva',
                'password' => 'password',
            ]
        );

        $technician->syncRoles('technician');

        Employee::where('email', 'joao@empresa.com')
            ->whereNull('user_id')
            ->update(['user_id' => $technician->id]);
    }
}
