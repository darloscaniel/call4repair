<?php

namespace Tests\Feature;

use App\Models\Call;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CallTest extends TestCase
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
        return $this->tokenFor($this->userWithRole($roles));
    }

    private function userWithRole(string|array $roles): User
    {
        $user = User::factory()->create();
        $user->syncRoles($roles);

        return $user;
    }

    private function tokenFor(User $user): array
    {
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser($user)];
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Cliente Teste',
            'phone'         => '11912345678',
            'description'   => 'Problema na lataria',
            'status'        => 'open',
        ], $overrides);
    }

    public function test_public_can_open_a_call_without_authentication(): void
    {
        $this->postJson('/api/calls', $this->validPayload())
            ->assertStatus(201)
            ->assertJsonPath('customer_name', 'Cliente Teste');

        $this->assertDatabaseHas('calls', ['customer_name' => 'Cliente Teste']);
    }

    public function test_public_call_creation_is_rate_limited(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/calls', $this->validPayload(['customer_name' => "Cliente {$i}"]))
                ->assertStatus(201);
        }

        // A 6ª requisição no mesmo minuto deve ser bloqueada.
        $this->postJson('/api/calls', $this->validPayload(['customer_name' => 'Excedente']))
            ->assertStatus(429);
    }

    public function test_store_validates_status_enum(): void
    {
        $this->postJson('/api/calls', $this->validPayload(['status' => 'invalid_status']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_listing_calls_requires_authentication(): void
    {
        $this->getJson('/api/calls')->assertStatus(401);
    }

    public function test_technician_cannot_delete_calls(): void
    {
        $call = Call::factory()->create();

        // Technician can view/manage calls but lacks "delete calls".
        $this->withHeaders($this->authHeaders('technician'))
            ->deleteJson("/api/calls/{$call->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('calls', ['id' => $call->id]);
    }

    public function test_technician_sees_only_assigned_calls(): void
    {
        $tech = $this->userWithRole('technician');
        $employee = Employee::factory()->create(['user_id' => $tech->id]);

        $assigned = Call::factory()->create();
        $assigned->employees()->attach($employee->id);
        Call::factory()->count(2)->create(); // not assigned to this technician

        $this->withHeaders($this->tokenFor($tech))
            ->getJson('/api/calls')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $assigned->id);
    }

    public function test_technician_cannot_view_unassigned_call(): void
    {
        $tech = $this->userWithRole('technician');
        Employee::factory()->create(['user_id' => $tech->id]);
        $other = Call::factory()->create();

        $this->withHeaders($this->tokenFor($tech))
            ->getJson("/api/calls/{$other->id}")
            ->assertStatus(403);
    }

    public function test_admin_sees_all_calls(): void
    {
        Call::factory()->count(3)->create();

        $this->withHeaders($this->authHeaders('admin'))
            ->getJson('/api/calls')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_list_calls_with_employees(): void
    {
        $call     = Call::factory()->create();
        $employee = Employee::factory()->create();
        $call->employees()->attach($employee->id, ['assigned_at' => now(), 'status' => 'ativo']);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/calls')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.employees.0.id', $employee->id)
            ->assertJsonStructure(['data', 'meta' => ['total', 'current_page', 'per_page']]);
    }

    public function test_update_syncs_employees(): void
    {
        $call      = Call::factory()->create(['status' => 'open']);
        $employees = Employee::factory()->count(2)->create();

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/calls/{$call->id}", [
                'status'    => 'in_progress',
                'employees' => $employees->pluck('id')->all(),
            ])
            ->assertOk()
            ->assertJsonPath('status', 'in_progress')
            ->assertJsonCount(2, 'employees');

        $this->assertDatabaseHas('call_employee', [
            'call_id'     => $call->id,
            'employee_id' => $employees->first()->id,
        ]);
    }

    public function test_missing_call_returns_json_404(): void
    {
        $this->withHeaders($this->authHeaders())
            ->getJson('/api/calls/999999')
            ->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    public function test_api_errors_are_json_even_without_accept_header(): void
    {
        // No Accept: application/json header — the api/* rule must still
        // produce a JSON error (assertJsonStructure fails on an HTML body).
        $this->get('/api/calls/999999', $this->authHeaders())
            ->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    public function test_can_delete_call(): void
    {
        $call     = Call::factory()->create();
        $employee = Employee::factory()->create();
        $call->employees()->attach($employee->id);

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/calls/{$call->id}")
            ->assertNoContent();

        // Soft delete: the row is kept (with deleted_at) and so is its history,
        // but it no longer shows up in the default listing.
        $this->assertSoftDeleted('calls', ['id' => $call->id]);
        $this->assertDatabaseHas('call_employee', ['call_id' => $call->id]);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/calls')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_update_rejects_invalid_status_transition(): void
    {
        $call = Call::factory()->create(['status' => 'done']);

        // done is terminal — reopening it is not a valid transition.
        $this->withHeaders($this->authHeaders())
            ->putJson("/api/calls/{$call->id}", ['status' => 'open'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('calls', ['id' => $call->id, 'status' => 'done']);
    }

    public function test_update_allows_same_status_when_only_assigning_employees(): void
    {
        $call     = Call::factory()->create(['status' => 'done']);
        $employee = Employee::factory()->create();

        // Keeping the same (terminal) status while reassigning must be allowed.
        $this->withHeaders($this->authHeaders())
            ->putJson("/api/calls/{$call->id}", [
                'status'    => 'done',
                'employees' => [$employee->id],
            ])
            ->assertOk()
            ->assertJsonCount(1, 'employees');
    }
}
