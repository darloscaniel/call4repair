<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CallEmployee;

class CallEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('call_employee')->insert([
            [
                'call_id' => 1,
                'employee_id' => 1,
                'assigned_at' => now(),
                'status' => 'ativo',
            ],
            [
                'call_id' => 1,
                'employee_id' => 2,
                'assigned_at' => now(),
                'status' => 'ativo',
            ],
            [
                'call_id' => 2,
                'employee_id' => 3,
                'assigned_at' => now(),
                'status' => 'pendente',
            ],
        ]);
    }
}
