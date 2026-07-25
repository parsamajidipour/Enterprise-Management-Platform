<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvidenceFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'file_name'   => $this->file_name,
            'file_type'   => $this->file_type,
            'file_size'   => $this->file_size,
            'url'         => asset('storage/' . $this->file_path),
            'uploader'    => new UserResource($this->whenLoaded('uploader')),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
