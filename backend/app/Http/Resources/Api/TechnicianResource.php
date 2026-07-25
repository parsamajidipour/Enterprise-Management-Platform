<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnicianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'profile' => new TechnicianProfileResource($this->whenLoaded('technicianProfile')),
            'latest_location' => new TechnicianLocationResource($this->whenLoaded('latestTechnicianLocation')),
            'active_jobs_count' => $this->when(isset($this->active_jobs_count), $this->active_jobs_count),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
