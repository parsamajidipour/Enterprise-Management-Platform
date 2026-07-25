<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'status'          => $this->status,
            'notes'           => $this->notes,
            'inspected_at'    => $this->inspected_at?->toISOString(),
            'asset'           => new AssetResource($this->whenLoaded('asset')),
            'inspection_form' => new InspectionFormResource($this->whenLoaded('inspectionForm')),
            'work_order'      => new WorkOrderResource($this->whenLoaded('workOrder')),
            'inspector'       => new UserResource($this->whenLoaded('inspector')),
            'answers'         => InspectionAnswerResource::collection($this->whenLoaded('answers')),
            'defects'         => DefectResource::collection($this->whenLoaded('defects')),
            'evidence_files'  => EvidenceFileResource::collection($this->whenLoaded('evidenceFiles')),
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
