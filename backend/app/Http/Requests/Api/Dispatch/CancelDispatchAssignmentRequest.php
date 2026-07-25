<?php

namespace App\Http\Requests\Api\Dispatch;

use Illuminate\Foundation\Http\FormRequest;

class CancelDispatchAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
        ];
    }
}
