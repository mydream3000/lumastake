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
            return;
        }

        // Reject control characters (except tabs/newlines if allowed)
        $pattern = $this->allowNewlines
            ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/'
            : '/[\x00-\x1F\x7F]/';
        if (preg_match($pattern, $value)) {
            $fail('The :attribute contains invalid characters.');
            return;
        }

        // Reject null bytes (even URL-encoded)
        if (str_contains($value, "\x00") || stripos($value, '%00') !== false) {
            $fail('The :attribute contains invalid characters.');
            return;
        }

        // Disallow HTML tags (strip_tags is case-insensitive, handles <sCrIpT> etc.)
        if ($value !== strip_tags($value)) {
            $fail('The :attribute must not contain HTML tags.');
            return;
        }

        // Decode HTML entities and check again for hidden tags
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($decoded !== strip_tags($decoded)) {
            $fail('The :attribute contains encoded HTML.');
            return;
        }

        // Decode hex/decimal HTML entities manually (&# patterns)
        $entityDecoded = preg_replace_callback('/&#x?[0-9a-fA-F]+;?/', function ($match) {
            return html_entity_decode($match[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }, $value);
        if ($entityDecoded !== strip_tags($entityDecoded)) {
            $fail('The :attribute contains encoded HTML.');
            return;
        }

        // Disallow event handlers (onclick=, onerror=, onload=, etc.)
        if (preg_match('/\bon[a-z]+\s*=/iu', $value)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }

        // Disallow dangerous protocols (with obfuscation resistance)
        $normalized = preg_replace('/[\s\x00-\x1F]+/', '', $value); // strip whitespace/control chars
        $normalized = preg_replace('/&#x?[0-9a-fA-F]+;?/i', '', $normalized); // strip HTML entities
        if (preg_match('/javascript|vbscript|data\s*:|livescript|expression\s*\(/iu', $normalized)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }

        // Also check the original value for protocol patterns (with whitespace tolerance)
        if (preg_match('/j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:/iu', $value)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }

        // Disallow base64 data URIs
        if (preg_match('/data\s*:[^,]*;?\s*base64/iu', $value)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }

        // Disallow SVG/XML injection patterns
        if (preg_match('/<\s*\/?\s*(svg|xml|math|iframe|object|embed|applet|form|input|button|select|textarea|marquee|bgsound|meta|link|style|base)\b/iu', $value)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }

        // Disallow CSS expression patterns
        if (preg_match('/expression\s*\(|url\s*\(\s*(javascript|data|vbscript)/iu', $value)) {
            $fail('The :attribute contains disallowed content.');
            return;
        }
    }
}
