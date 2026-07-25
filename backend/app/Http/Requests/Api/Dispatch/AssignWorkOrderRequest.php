<?php

namespace App\Http\Requests\Api\Dispatch;

use Illuminate\Foundation\Http\FormRequest;

class AssignWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'technician_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
