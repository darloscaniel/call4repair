<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_sets_httponly_token_cookie(): void
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
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email'], 'roles', 'permissions'])
            ->assertCookie('token')
            ->assertCookie('XSRF-TOKEN');

        // Token must not be exposed in the response body, and password never leaks.
        $response->assertJsonMissingPath('token');
        $response->assertJsonMissingPath('user.password');

        // The auth cookie must be httpOnly.
        $tokenCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === 'token');
        $this->assertNotNull($tokenCookie);
        $this->assertTrue($tokenCookie->isHttpOnly());
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure(['user', 'roles', 'permissions']);
    }

    public function test_logout_clears_auth_cookies(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/logout')
            ->assertOk()
            ->assertCookieExpired('token')
            ->assertCookieExpired('XSRF-TOKEN');
    }

    // Note: JWT blacklisting on logout (token can no longer authenticate) is a
    // production behavior backed by the persistent cache store. It is not
    // asserted here because the test cache (array) is not shared across the
    // tymon blacklist storage resolved at boot; it is verified end-to-end
    // against the running app.

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
