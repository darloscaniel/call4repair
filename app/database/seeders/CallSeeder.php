<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Call;

class CallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Call::insert([
            [
                'customer_name' => 'Ana Pereira',
                'description' => 'Arranhão na porta traseira',
                'status' => 'aberto',
            ],
            [
                'customer_name' => 'Bruno Gomes',
                'description' => 'Troca de para-choque',
                'status' => 'em_andamento',
            ],
            [
                'customer_name' => 'Carlos Martins',
                'description' => 'Pintura do capô',
                'status' => 'aberto',
            ],
        ]);
    }
}
