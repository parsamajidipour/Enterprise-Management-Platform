<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DispatchAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'assigned_at' => $this->assigned_at?->toISOString(),
            'accepted_at' => $this->accepted_at?->toISOString(),
            'arrived_at' => $this->arrived_at?->toISOString(),
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'notes' => $this->notes,
            'work_order' => new WorkOrderResource($this->whenLoaded('workOrder')),
            'technician' => new UserResource($this->whenLoaded('technician')),
            'assigned_by' => new UserResource($this->whenLoaded('assignedBy')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
