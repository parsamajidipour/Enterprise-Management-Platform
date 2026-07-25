<?php

namespace App\Http\Requests\Api\Auth;

use App\Rules\NoCrlf;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'email' rule alone is not enough on this Laravel 10.x pin — see
            // CVE-2026-48019 and NoCrlf's docblock.
            'email'    => ['required', 'email', new NoCrlf()],
            'password' => ['required', 'string'],
        ];
    }
}
