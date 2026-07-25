<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'status'       => $this->status,
            'priority'     => $this->priority,
            'source'       => $this->source,
            'external_id'  => $this->external_id,
            'external_reference' => $this->external_reference,
            'outage_type'  => $this->outage_type,
            'reported_at'  => $this->reported_at?->toISOString(),
            'cmms_status'  => $this->cmms_status,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'asset'        => new AssetResource($this->whenLoaded('asset')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'assignee'     => new UserResource($this->whenLoaded('assignee')),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
