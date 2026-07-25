<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnicianAvailabilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'technician' => new TechnicianResource($this->resource),
            'availability' => ((int) ($this->active_jobs_count ?? 0)) === 0 ? 'available' : 'busy',
            'active_jobs_count' => (int) ($this->active_jobs_count ?? 0),
            'latest_location' => new TechnicianLocationResource($this->whenLoaded('latestTechnicianLocation')),
        ];
    }
}
