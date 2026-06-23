<?php

namespace Tests\Feature;

use App\Models\Call;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CallTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(): array
    {
        $user  = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['Authorization' => "Bearer {$token}"];
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Cliente Teste',
            'phone'         => '11912345678',
            'description'   => 'Problema na lataria',
            'status'        => 'aberto',
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
        $this->postJson('/api/calls', $this->validPayload(['status' => 'invalido']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_listing_calls_requires_authentication(): void
    {
        $this->getJson('/api/calls')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_calls_with_employees(): void
    {
        $call     = Call::factory()->create();
        $employee = Employee::factory()->create();
        $call->employees()->attach($employee->id, ['assigned_at' => now(), 'status' => 'ativo']);

        $this->withHeaders($this->authHeaders())
            ->getJson('/api/calls')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.employees.0.id', $employee->id);
    }

    public function test_update_syncs_employees(): void
    {
        $call      = Call::factory()->create(['status' => 'aberto']);
        $employees = Employee::factory()->count(2)->create();

        $this->withHeaders($this->authHeaders())
            ->putJson("/api/calls/{$call->id}", [
                'status'    => 'em_andamento',
                'employees' => $employees->pluck('id')->all(),
            ])
            ->assertOk()
            ->assertJsonPath('status', 'em_andamento')
            ->assertJsonCount(2, 'employees');

        $this->assertDatabaseHas('call_employee', [
            'call_id'     => $call->id,
            'employee_id' => $employees->first()->id,
        ]);
    }

    public function test_can_delete_call(): void
    {
        $call     = Call::factory()->create();
        $employee = Employee::factory()->create();
        $call->employees()->attach($employee->id);

        $this->withHeaders($this->authHeaders())
            ->deleteJson("/api/calls/{$call->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('calls', ['id' => $call->id]);
        $this->assertDatabaseMissing('call_employee', ['call_id' => $call->id]);
    }
}
