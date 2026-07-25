<?php

namespace App\Http\Requests\Api\Asset;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'code'                => ['required', 'string', 'max:100', 'unique:assets,code'],
            'asset_category_id'   => ['nullable', 'integer', 'exists:asset_categories,id'],
            'description'         => ['nullable', 'string'],
            'location'            => ['nullable', 'string', 'max:255'],
            'status'              => ['sometimes', 'in:active,inactive,under_maintenance'],
            'purchased_at'        => ['nullable', 'date'],
        ];
    }
}
