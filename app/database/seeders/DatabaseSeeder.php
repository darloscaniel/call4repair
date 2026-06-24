<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            // Employees before users so the technician account can be linked
            // to an existing employee record.
            EmployeeSeeder::class,
            UserSeeder::class,
            CallSeeder::class,
            CallEmployeeSeeder::class,
        ]);
    }
}
