<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token(): void
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'message', 'user' => ['id', 'name', 'email']]);

        // A senha nunca deve vazar na resposta.
        $response->assertJsonMissingPath('user.password');
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $this->postJson('/api/login', [
            'email'    => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401);
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_protected_route_requires_authentication(): void
    {
        $this->getJson('/api/calls')->assertStatus(401);
        $this->getJson('/api/employees')->assertStatus(401);
    }
}
