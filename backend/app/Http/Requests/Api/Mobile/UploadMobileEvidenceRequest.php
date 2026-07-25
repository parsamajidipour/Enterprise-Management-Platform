<?php

namespace App\Http\Requests\Api\Mobile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadMobileEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'max:10240'],
            'files' => ['nullable', 'array', 'min:1'],
            'files.*' => ['file', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->hasFile('file') && !$this->hasFile('files')) {
                $validator->errors()->add('file', 'Please upload at least one evidence file.');
            }
        });
    }

    public function evidenceFiles(): array
    {
        if ($this->hasFile('files')) {
            $files = $this->file('files');
            return is_array($files) ? $files : [$files];
        }

        return $this->hasFile('file') ? [$this->file('file')] : [];
    }
}
