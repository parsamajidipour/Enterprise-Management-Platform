<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Mitigates CVE-2026-48019 (CRLF injection in Laravel's built-in `email`
 * validation rule, CWE-93) for the current laravel/framework ^10.10 pin.
 * The framework fix only shipped in 12.60.0 / 13.10.0; see SECURITY.md for
 * why this project cannot take that upgrade right now. Apply this rule
 * alongside `email` on every email-typed input to reject \r / \n before the
 * value can reach anything that treats it as a header (e.g. Bcc injection).
 */
class NoCrlf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match('/\r|\n/', $value)) {
            $fail('The :attribute field contains invalid characters.');
        }
    }
}
