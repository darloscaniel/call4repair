<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['name' => 'Carlos Daniel', 'age' => 21, 'phone' => '98798798711', 'email' => 'carlos@examplee.com'],
            ['name' => 'João Silva',    'age' => 32, 'phone' => '11999990001', 'email' => 'joao@empresa.com'],
            ['name' => 'Maria Souza',   'age' => 28, 'phone' => '11999990002', 'email' => 'maria@empresa.com'],
        ];

        foreach ($employees as $employee) {
            Employee::firstOrCreate(
                ['email' => $employee['email']],
                $employee
            );
        }
    }
}
