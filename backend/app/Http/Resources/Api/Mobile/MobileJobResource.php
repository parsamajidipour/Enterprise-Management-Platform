<?php

namespace App\Http\Resources\Api\Mobile;

use App\Http\Resources\Api\AssetResource;
use App\Http\Resources\Api\EvidenceFileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $workOrder = $this->whenLoaded('workOrder');

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
            'work_order' => $workOrder ? [
                'id' => $this->workOrder->id,
                'title' => $this->workOrder->title,
                'description' => $this->workOrder->description,
                'status' => $this->workOrder->status,
                'priority' => $this->workOrder->priority,
                'source' => $this->workOrder->source,
                'external_id' => $this->workOrder->external_id,
                'external_reference' => $this->workOrder->external_reference,
                'outage_type' => $this->workOrder->outage_type,
                'reported_at' => $this->workOrder->reported_at?->toISOString(),
                'cmms_status' => $this->workOrder->cmms_status,
                'location' => [
                    'latitude' => $this->workOrder->latitude,
                    'longitude' => $this->workOrder->longitude,
                ],
                'scheduled_at' => $this->workOrder->scheduled_at?->toISOString(),
                'completed_at' => $this->workOrder->completed_at?->toISOString(),
                'asset' => new AssetResource($this->workOrder->asset),
            ] : null,
            'evidence_files' => EvidenceFileResource::collection($this->whenLoaded('evidenceFiles')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
