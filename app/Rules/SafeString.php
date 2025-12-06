<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeString implements ValidationRule
{
    public function __construct(
        protected bool $allowNewlines = true
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return; // other validators (string) will handle types
        }

        // Reject control characters (except tabs/newlines if allowed)
        $pattern = $this->allowNewlines
            ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/'
            : '/[\x00-\x1F\x7F]/';
        if (preg_match($pattern, $value)) {
            $fail('The :attribute contains invalid control characters.');
            return;
        }

        // Disallow HTML tags
        if ($value !== strip_tags($value)) {
            $fail('The :attribute must not contain HTML.');
            return;
        }

        // Disallow obvious handler/protocol tokens
        if (preg_match('/\bon[a-zA-Z]+\s*=|javascript\s*:|vbscript\s*:|data\s*:/iu', $value)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }
    }
}
