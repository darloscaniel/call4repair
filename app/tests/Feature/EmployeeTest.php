<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function authHeaders(string|array $roles = 'admin'): array
    {
        $user = User::factory()->create();
        $user->syncRoles($roles);
        $token = JWTAuth::fromUser($user);

        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_index_requires_authentication(): void
    {
        $this->getJson('/api/employees')->assertStatus(401);
    }

    public function test_technician_cannot_manage_employees(): void
    {
        // A technician lacks the "manage employees" permission.
        $this->withHeaders($this->authHeaders('technician'))
            ->getJson('/api/employees')
            ->assertStatus(403);
    }

    public function test_authenticated_user_can_list_employees(): void
    {
        Employee::factory()->count(3)->create();

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/employees')
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_can_create_employee(): void
    {
        $payload = [
            'name'  => 'Fulano de Tal',
            'age'   => 30,
            'phone' => '11999998888',
            'email' => 'fulano@example.com',
        ];

        $this->withHeaders($this->authHeaders())
            ->postJson('/api/employees', $payload)
            ->assertStatus(201)
            ->assertJsonPath('name', 'Fulano de Tal');

        $this->assertDatabaseHas('employees', ['email' => 'fulano@example.com']);
    }

    public function test_create_employee_validates_required_fields(): void
    {
        $this->withHeaders($this->authHeaders())
            ->postJson('/api/employees', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'age', 'phone', 'email']);
    }

    public function test_create_employee_rejects_duplicate_email(): void
    {
        Employee::factory()->create(['email' => 'dup@example.com']);

        $this->withHeaders($this->authHeaders())
            ->postJson('/api/employees', [
                'name'  => 'Outro',
                'age'   => 25,
                'phone' => '11900000000',
                'email' => 'dup@example.com',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_update_employee(): void
    {
        $employee = Employee::factory()->create(['name' => 'Antigo']);

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/employees/{$employee->id}", ['name' => 'Atualizado'])
            ->assertOk()
            ->assertJsonPath('name', 'Atualizado');

        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'name' => 'Atualizado']);
    }

    public function test_update_keeps_own_email_unique_rule(): void
    {
        $employee = Employee::factory()->create(['email' => 'self@example.com']);

        // Atualizar mantendo o próprio e-mail não deve disparar erro de unicidade.
        $this->withHeaders($this->authHeaders())
            ->putJson("/api/employees/{$employee->id}", [
                'name'  => 'Mesmo Email',
                'email' => 'self@example.com',
            ])
            ->assertOk();
    }

    public function test_can_delete_employee(): void
    {
        $employee = Employee::factory()->create();

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/employees/{$employee->id}")
            ->assertOk();

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }
}
