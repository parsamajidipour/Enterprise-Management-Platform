<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'inspection_form_field_id' => $this->inspection_form_field_id,
            'field'                    => new InspectionFormFieldResource($this->whenLoaded('field')),
            'value'                    => $this->value,
        ];
    }
}
