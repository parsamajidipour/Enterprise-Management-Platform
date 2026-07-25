<?php

namespace App\Http\Requests\Api\InspectionRecord;

use Illuminate\Foundation\Http\FormRequest;

class UploadEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files'   => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'max:20480', 'mimes:jpeg,png,jpg,gif,mp4,pdf'],
        ];
    }
}
