<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'code'         => $this->code,
            'description'  => $this->description,
            'location'     => $this->location,
            'status'       => $this->status,
            'purchased_at' => $this->purchased_at?->toDateString(),
            'category'     => new AssetCategoryResource($this->whenLoaded('category')),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
