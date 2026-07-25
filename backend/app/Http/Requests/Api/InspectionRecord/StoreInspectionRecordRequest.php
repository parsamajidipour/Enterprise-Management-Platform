<?php

namespace App\Http\Requests\Api\InspectionRecord;

use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inspection_form_id'          => ['required', 'integer', 'exists:inspection_forms,id'],
            'asset_id'                    => ['required', 'integer', 'exists:assets,id'],
            'work_order_id'               => ['nullable', 'integer', 'exists:work_orders,id'],
            'status'                      => ['sometimes', 'in:draft,submitted,reviewed'],
            'notes'                       => ['nullable', 'string'],
            'inspected_at'                => ['nullable', 'date'],
            'answers'                     => ['sometimes', 'array'],
            'answers.*.inspection_form_field_id' => ['required_with:answers', 'integer', 'exists:inspection_form_fields,id'],
            'answers.*.value'             => ['nullable', 'string'],
        ];
    }
}
