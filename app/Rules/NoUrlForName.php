<?php

namespace App\Rules;

use App\Support\Sanitizer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoUrlForName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return; // other validators will handle types
        }

        // Normalize and decode to catch obfuscated patterns
        $s = Sanitizer::normalizeUnicode($value);
        $s = Sanitizer::decodeHtmlEntities($s);
        $s = Sanitizer::removeControlChars($s);
        // Map fullwidth punctuation to ascii to avoid bypass
        $s = strtr($s, ["：" => ":", "．" => ".", "｡" => ".", "。" => "."]);

        // Basic fast checks
        $lower = mb_strtolower($s, 'UTF-8');
        if (str_contains($lower, 'http://') || str_contains($lower, 'https://') || str_contains($lower, 'www.')) {
            $fail('The :attribute must not contain links.');
            return;
        }

        // Detect common domain patterns including IDN punycode and shortlinks (t.me, bit.ly, etc.)
        $domainLike = '/(^|\s)([a-z0-9_-]+\.)+[a-z]{2,}(\/|\b)/iu';
        if (preg_match($domainLike, $s)) {
            $fail('The :attribute must not contain links.');
            return;
        }

        // Detect explicit URL schemes and mailto
        if (preg_match('/\b(?:https?|ftp|mailto|javascript|data|vbscript)\s*:/iu', $s)) {
            $fail('The :attribute must not contain links.');
            return;
        }

        // Detect punycode (IDN) hosts
        if (preg_match('/\bxn--[a-z0-9-]+/iu', $s)) {
            $fail('The :attribute must not contain links.');
            return;
        }
    }
}
