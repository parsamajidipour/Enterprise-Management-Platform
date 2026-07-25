<?php

namespace App\Http\Requests\Api\InspectionForm;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInspectionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['sometimes', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'asset_category_id' => ['nullable', 'integer', 'exists:asset_categories,id'],
            'is_active'         => ['sometimes', 'boolean'],
        ];
    }
}
