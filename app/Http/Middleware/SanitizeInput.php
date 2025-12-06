<?php

namespace App\Http\Middleware;

use App\Support\Sanitizer;
use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Fields that should NOT be sanitized/transformed.
     * - Passwords/secrets
     * - Binary/file fields (handled by Laravel)
     * - Admin SEO files where XML/robots text is expected
     * - Potential rich-text fields (content/html/body) if appear
     */
    protected array $excludedKeys = [
        'password', 'password_confirmation', 'current_password',
        'file', 'files', 'avatar', 'image', 'images', 'attachment',
        'robots', 'sitemap', 'robots_txt', 'schema_json',
        'content', 'html', 'body',
    ];

    /**
     * Routes (by name) that should be excluded from sanitation of specific fields.
     */
    protected array $excludedRoutes = [
        'admin.seo.update-home',
        'admin.seo.update-robots',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!config('app.sanitize_input', true) || !filter_var(env('SANITIZE_INPUT', true), FILTER_VALIDATE_BOOLEAN)) {
            return $next($request);
        }

        // Skip for specific named routes if needed
        $routeName = optional($request->route())->getName();

        $input = $request->all();
        $sanitized = $this->sanitizeArray($input, $routeName);

        // Replace only if changed to avoid unexpected type casts
        if ($sanitized !== $input) {
            $request->merge($sanitized);
        }

        // Also sanitize query string values
        if ($request->query->count() > 0) {
            $query = $request->query->all();
            $sanQ = $this->sanitizeArray($query, $routeName);
            if ($sanQ !== $query) {
                $request->query->replace($sanQ);
            }
        }

        return $next($request);
    }

    protected function sanitizeArray(array $data, ?string $routeName): array
    {
        foreach ($data as $key => $value) {
            if ($this->isExcludedKey($key) || $this->isExcludedRoute($routeName)) {
                // Do not sanitize excluded fields
                continue;
            }

            if (is_string($value)) {
                $data[$key] = Sanitizer::sanitizeString($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value, $routeName);
            }
            // Objects/files are left intact
        }
        return $data;
    }

    protected function isExcludedKey(string $key): bool
    {
        return in_array($key, $this->excludedKeys, true);
    }

    protected function isExcludedRoute(?string $routeName): bool
    {
        if (!$routeName) return false;
        return in_array($routeName, $this->excludedRoutes, true);
    }
}
