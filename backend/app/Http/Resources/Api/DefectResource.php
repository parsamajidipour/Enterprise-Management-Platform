<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'severity'    => $this->severity,
            'description' => $this->description,
            'status'      => $this->status,
            'field'       => new InspectionFormFieldResource($this->whenLoaded('field')),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
