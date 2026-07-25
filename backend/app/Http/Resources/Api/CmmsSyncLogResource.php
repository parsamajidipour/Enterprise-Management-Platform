<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CmmsSyncLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'direction' => $this->direction,
            'action' => $this->action,
            'external_id' => $this->external_id,
            'local_type' => $this->local_type,
            'local_id' => $this->local_id,
            'status' => $this->status,
            'request_payload' => $this->request_payload,
            'response_payload' => $this->response_payload,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
