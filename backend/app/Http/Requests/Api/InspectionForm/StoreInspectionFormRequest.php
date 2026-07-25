<?php

namespace App\Http\Requests\Api\InspectionForm;

use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'asset_category_id'     => ['nullable', 'integer', 'exists:asset_categories,id'],
            'is_active'             => ['sometimes', 'boolean'],
            'fields'                => ['sometimes', 'array'],
            'fields.*.label'        => ['required_with:fields', 'string', 'max:255'],
            'fields.*.field_type'   => ['required_with:fields', 'in:text,number,boolean,select,textarea,photo'],
            'fields.*.options'      => ['nullable', 'array'],
            'fields.*.is_required'  => ['sometimes', 'boolean'],
            'fields.*.order'        => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
