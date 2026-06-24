<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Call;

class CallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $calls = [
            ['customer_name' => 'Ana Pereira',    'phone' => '11988880001', 'description' => 'Arranhão na porta traseira', 'status' => 'open'],
            ['customer_name' => 'Bruno Gomes',    'phone' => '11988880002', 'description' => 'Troca de para-choque',       'status' => 'in_progress'],
            ['customer_name' => 'Carlos Martins', 'phone' => '11988880003', 'description' => 'Pintura do capô',            'status' => 'open'],
        ];

        foreach ($calls as $call) {
            Call::firstOrCreate(
                ['customer_name' => $call['customer_name']],
                $call
            );
        }
    }
}
