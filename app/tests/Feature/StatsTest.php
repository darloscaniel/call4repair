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

class StatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function tokenFor(string $role): array
    {
        $user = User::factory()->create();
        $user->syncRoles($role);

        return ['user' => $user, 'headers' => ['Authorization' => 'Bearer ' . JWTAuth::fromUser($user)]];
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/stats')->assertStatus(401);
    }

    public function test_admin_gets_global_aggregates(): void
    {
        Call::factory()->create(['status' => 'open']);
        Call::factory()->create(['status' => 'done']);

        $admin = $this->tokenFor('admin');

        $this->withHeaders($admin['headers'])
            ->getJson('/api/stats')
            ->assertOk()
            ->assertJsonStructure([
                'totals'       => ['calls', 'open', 'employees'],
                'by_status'    => ['open', 'in_progress', 'done', 'rejected'],
                'per_day',
                'per_employee',
                'can_view_all',
            ])
            ->assertJsonPath('can_view_all', true)
            ->assertJsonPath('by_status.open', 1)
            ->assertJsonPath('by_status.done', 1)
            ->assertJsonPath('totals.calls', 2);
    }

    public function test_technician_aggregates_are_scoped_to_assigned_calls(): void
    {
        $tech = $this->tokenFor('technician');
        $employee = Employee::factory()->create(['user_id' => $tech['user']->id]);

        $assigned = Call::factory()->create(['status' => 'open']);
        $assigned->employees()->attach($employee->id);
        Call::factory()->count(3)->create(['status' => 'open']); // someone else's

        $this->withHeaders($tech['headers'])
            ->getJson('/api/stats')
            ->assertOk()
            ->assertJsonPath('can_view_all', false)
            ->assertJsonPath('totals.calls', 1)
            ->assertJsonPath('by_status.open', 1)
            ->assertJsonPath('per_employee', []);
    }
}
