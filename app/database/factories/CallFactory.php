<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Call>
 */
class CallFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'phone'         => fake()->numerify('###########'),
            'description'   => fake()->sentence(),
            'status'        => fake()->randomElement(['aberto', 'em_andamento', 'concluido', 'recusado']),
        ];
    }
}
