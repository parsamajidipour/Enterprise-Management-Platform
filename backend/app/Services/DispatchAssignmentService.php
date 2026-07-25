<?php

namespace App\Services;

use App\Models\DispatchAssignment;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderStatusEvent;
use App\Services\Cmms\CmmsPushbackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispatchAssignmentService
{
    public function __construct(private readonly CmmsPushbackService $cmmsPushback)
    {
    }

    public function assign(WorkOrder $workOrder, User $technician, ?User $assignedBy, ?string $notes = null): DispatchAssignment
    {
        if (!$technician->hasRole('technician') || !$technician->technicianProfile?->is_active) {
            throw ValidationException::withMessages([
                'technician_id' => ['Selected user is not an active technician.'],
            ]);
        }

        $assignment = DB::transaction(function () use ($workOrder, $technician, $assignedBy, $notes) {
            $workOrder = WorkOrder::whereKey($workOrder->id)->lockForUpdate()->firstOrFail();

            if (in_array($workOrder->status, WorkOrder::CLOSED_STATUSES, true)) {
                throw ValidationException::withMessages([
                    'work_order_id' => ['This work order is already closed and cannot be assigned.'],
                ]);
            }

            if ($workOrder->dispatchAssignments()->active()->exists()) {
                throw ValidationException::withMessages([
                    'work_order_id' => ['This work order already has an active assignment.'],
                ]);
            }

            $fromStatus = $workOrder->status;

            $assignment = DispatchAssignment::create([
                'work_order_id' => $workOrder->id,
                'technician_id' => $technician->id,
                'assigned_by' => $assignedBy?->id,
                'status' => DispatchAssignment::STATUS_CREATED,
                'assigned_at' => now(),
                'notes' => $notes,
            ]);

            $workOrder->update([
                'assigned_to' => $technician->id,
                'status' => 'assigned',
            ]);

            $this->recordStatusEvent($workOrder->fresh(), $fromStatus, 'assigned', $assignedBy, $notes);

            return $assignment->load(['workOrder.asset', 'technician', 'assignedBy']);
        });

        $this->cmmsPushback->pushWorkOrderStatus($assignment->workOrder, 'assigned', [
            'assignment_id' => $assignment->id,
            'technician_id' => $assignment->technician_id,
        ]);

        return $assignment;
    }

    public function cancel(DispatchAssignment $assignment, ?User $actor, ?string $notes = null): DispatchAssignment
    {
        $assignment = DB::transaction(function () use ($assignment, $actor, $notes) {
            $workOrder = $assignment->workOrder;
            $fromStatus = $workOrder->status;

            $assignment->update([
                'status' => DispatchAssignment::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'notes' => $notes ?? $assignment->notes,
            ]);

            $workOrder->update([
                'status' => 'pending_dispatch',
                'assigned_to' => null,
            ]);

            $this->recordStatusEvent($workOrder->fresh(), $fromStatus, 'pending_dispatch', $actor, $notes);

            return $assignment->fresh(['workOrder.asset', 'technician', 'assignedBy']);
        });

        $this->cmmsPushback->pushWorkOrderStatus($assignment->workOrder, 'pending_dispatch', [
            'assignment_id' => $assignment->id,
            'cancelled_at' => $assignment->cancelled_at?->toISOString(),
        ]);

        return $assignment;
    }

    private function recordStatusEvent(WorkOrder $workOrder, ?string $fromStatus, string $toStatus, ?User $actor, ?string $notes): void
    {
        WorkOrderStatusEvent::create([
            'work_order_id' => $workOrder->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'actor_id' => $actor?->id,
            'source' => 'admin',
            'notes' => $notes,
        ]);
    }
}
