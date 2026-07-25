<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InspectionFormFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'label'       => $this->label,
            'field_type'  => $this->field_type,
            'options'     => $this->options,
            'is_required' => $this->is_required,
            'order'       => $this->order,
        ];
    }
}
