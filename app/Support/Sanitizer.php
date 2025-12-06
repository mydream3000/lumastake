<?php

namespace App\Support;

/**
 * Sanitizer: conservative, idempotent input sanitizer.
 *
 * NOTE: This is not a full HTML sanitizer (we do not allow HTML at all for user inputs).
 * It targets plain-text fields and attempts to neutralize common XSS vectors and
 * weirdly encoded payloads: HTML entities, zero-width/control chars, dangerous protocols.
 */
class Sanitizer
{
    /**
     * Sanitize a string value.
     * - Trim and normalize Unicode (NFKC when intl is available)
     * - Decode HTML entities (&lt;script&gt; -> <script>)
     * - Remove control/zero-width characters (except newlines, tabs)
     * - Strip all HTML tags
     * - Remove inline event handler patterns (onerror=, onclick=) from remaining text
     * - Remove dangerous protocol tokens (javascript:, vbscript:, data:)
     * - Collapse excessive whitespace
     */
    public static function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $s = self::toUtf8($value);
        $s = trim($s);
        $s = self::normalizeUnicode($s);
        $s = self::decodeHtmlEntities($s);
        $s = self::removeControlChars($s);
        // No HTML allowed at all for user-provided text
        $s = strip_tags($s);
        // Remove inline handler tokens that could be used in some render contexts
        $s = preg_replace('/on[a-zA-Z]+\s*=\s*/u', '', $s) ?? $s;
        // Remove dangerous protocol markers even if not part of HTML
        $s = self::removeDangerousProtocols($s);
        $s = self::collapseWhitespace($s);

        return $s;
    }

    public static function decodeHtmlEntities(string $s): string
    {
        // Decode multiple times (capped) to handle nested encodings
        for ($i = 0; $i < 2; $i++) {
            $decoded = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $s) break;
            $s = $decoded;
        }
        return $s;
    }

    public static function normalizeUnicode(string $s): string
    {
        if (class_exists('Normalizer')) {
            try {
                return \Normalizer::normalize($s, \Normalizer::FORM_KC) ?: $s;
            } catch (\Throwable) {
                return $s;
            }
        }
        return $s;
    }

    public static function removeControlChars(string $s): string
    {
        // Allow tabs/newlines/carriage returns, remove other Cc/Cf (including zero-width joiners)
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? $s;
        // Common zero-width chars (Cf)
        $zeroWidth = "\x{200B}\x{200C}\x{200D}\x{FEFF}"; // ZWSP, ZWNJ, ZWJ, BOM
        $s = preg_replace("/[{$zeroWidth}]/u", '', $s) ?? $s;
        return $s;
    }

    public static function removeDangerousProtocols(string $s): string
    {
        // Normalize fullwidth variants of ':' and '.' to ASCII to detect obfuscation
        $s = strtr($s, ["：" => ":", "．" => ".", "｡" => ".", "。" => "."]);

        // Remove occurrences of javascript:, vbscript:, data: (case-insensitive)
        $patterns = [
            '/\bjavascript\s*:/iu',
            '/\bvbscript\s*:/iu',
            // Restrict data: only when clearly HTML or JS payloads are hinted
            '/\bdata\s*:\s*(text\/html|application\/javascript|image\/(svg\+xml))/iu',
        ];
        foreach ($patterns as $re) {
            $s = preg_replace($re, '', $s) ?? $s;
        }
        return $s;
    }

    public static function collapseWhitespace(string $s): string
    {
        // Collapse runs of spaces/tabs, but preserve newlines
        $s = preg_replace("/[\t ]{2,}/u", ' ', $s) ?? $s;
        // Trim each line
        $lines = array_map(static fn($ln) => trim($ln), preg_split("/\r?\n/", $s));
        return trim(implode("\n", $lines));
    }

    private static function toUtf8(string $s): string
    {
        if (!mb_detect_encoding($s, 'UTF-8', true)) {
            return mb_convert_encoding($s, 'UTF-8');
        }
        return $s;
    }
}
