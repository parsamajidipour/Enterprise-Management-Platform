<?php

namespace Tests\Feature;

use App\Models\TechnicianProfile;
use App\Models\User;
use App\Models\WorkOrder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispatchClosedWorkOrderGuardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $technician;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('admin');

        $this->technician = User::factory()->create(['is_active' => true]);
        $this->technician->assignRole('technician');
        TechnicianProfile::factory()->create([
            'user_id'   => $this->technician->id,
            'is_active' => true,
        ]);
    }

    private function assign(WorkOrder $workOrder): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->admin)
            ->postJson("/api/admin/work-orders/{$workOrder->id}/assign", [
                'technician_id' => $this->technician->id,
            ]);
    }

    public function test_cannot_assign_completed_work_order(): void
    {
        $workOrder = WorkOrder::factory()->completed()->create(['created_by' => $this->admin->id]);

        $this->assign($workOrder)
            ->assertStatus(422)
            ->assertJsonPath('errors.work_order_id.0', 'This work order is already closed and cannot be assigned.');
    }

    public function test_cannot_assign_synced_to_cmms_work_order(): void
    {
        $workOrder = WorkOrder::factory()->syncedToCmms()->create(['created_by' => $this->admin->id]);

        $this->assign($workOrder)
            ->assertStatus(422)
            ->assertJsonPath('errors.work_order_id.0', 'This work order is already closed and cannot be assigned.');
    }

    public function test_cannot_assign_cancelled_work_order(): void
    {
        $workOrder = WorkOrder::factory()->cancelled()->create(['created_by' => $this->admin->id]);

        $this->assign($workOrder)
            ->assertStatus(422)
            ->assertJsonPath('errors.work_order_id.0', 'This work order is already closed and cannot be assigned.');
    }

    public function test_can_assign_pending_dispatch_work_order(): void
    {
        $workOrder = WorkOrder::factory()->create([
            'created_by' => $this->admin->id,
            'status'     => 'pending_dispatch',
        ]);

        $this->assign($workOrder)
            ->assertStatus(201);
    }
}
