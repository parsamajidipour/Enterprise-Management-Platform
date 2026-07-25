<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => $this->description,
            'is_active'       => $this->is_active,
            'asset_category'  => new AssetCategoryResource($this->whenLoaded('assetCategory')),
            'fields'          => InspectionFormFieldResource::collection($this->whenLoaded('fields')),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
