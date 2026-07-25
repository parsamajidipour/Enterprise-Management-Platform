<?php

namespace App\Http\Requests\Api\Asset;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetId = $this->route('asset')?->id ?? $this->route('asset');

        return [
            'name'                => ['sometimes', 'string', 'max:255'],
            'code'                => ['sometimes', 'string', 'max:100', "unique:assets,code,{$assetId}"],
            'asset_category_id'   => ['nullable', 'integer', 'exists:asset_categories,id'],
            'description'         => ['nullable', 'string'],
            'location'            => ['nullable', 'string', 'max:255'],
            'status'              => ['sometimes', 'in:active,inactive,under_maintenance'],
            'purchased_at'        => ['nullable', 'date'],
        ];
    }
}
