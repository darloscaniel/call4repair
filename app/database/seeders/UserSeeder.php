<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Create the default system access user (idempotent).
     * Credentials: test@example.com / password
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => 'password', // hashed automaticamente pelo cast do model
            ]
        );
    }
}
