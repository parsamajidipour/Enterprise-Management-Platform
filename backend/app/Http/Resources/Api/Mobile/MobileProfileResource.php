<?php

namespace App\Http\Resources\Api\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'roles' => $this->getRoleNames(),
            'technician_profile' => $this->whenLoaded('technicianProfile', fn() => [
                'employee_code' => $this->technicianProfile?->employee_code,
                'skills' => $this->technicianProfile?->skills,
                'phone' => $this->technicianProfile?->phone,
                'default_area' => $this->technicianProfile?->default_area,
                'is_active' => $this->technicianProfile?->is_active,
            ]),
            'latest_location' => $this->whenLoaded('technicianLocations', fn() => $this->technicianLocations->first() ? [
                'latitude' => $this->technicianLocations->first()->latitude,
                'longitude' => $this->technicianLocations->first()->longitude,
                'accuracy' => $this->technicianLocations->first()->accuracy,
                'captured_at' => $this->technicianLocations->first()->captured_at?->toISOString(),
            ] : null),
        ];
    }
}
