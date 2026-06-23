<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'  => fake()->name(),
            'age'   => fake()->numberBetween(18, 65),
            'phone' => fake()->numerify('###########'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
