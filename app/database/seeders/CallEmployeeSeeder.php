<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Call;
use App\Models\Employee;

class CallEmployeeSeeder extends Seeder
{
    /**
     * Link employees to calls idempotently, resolving records by their
     * natural keys (not by hard-coded IDs).
     */
    public function run(): void
    {
        $ana   = Call::where('customer_name', 'Ana Pereira')->first();
        $bruno = Call::where('customer_name', 'Bruno Gomes')->first();

        $carlos = Employee::where('email', 'carlos@examplee.com')->first();
        $joao   = Employee::where('email', 'joao@empresa.com')->first();
        $maria  = Employee::where('email', 'maria@empresa.com')->first();

        if ($ana && $carlos && $joao) {
            $ana->employees()->syncWithoutDetaching([
                $carlos->id => ['assigned_at' => now(), 'status' => 'ativo'],
                $joao->id   => ['assigned_at' => now(), 'status' => 'ativo'],
            ]);
        }

        if ($bruno && $maria) {
            $bruno->employees()->syncWithoutDetaching([
                $maria->id => ['assigned_at' => now(), 'status' => 'pendente'],
            ]);
        }
    }
}
