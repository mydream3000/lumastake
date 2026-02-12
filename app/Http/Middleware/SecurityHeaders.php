<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS filter in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy - don't leak full URL to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy - restrict browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com https://cdn.tiny.cloud https://widget.intercom.io https://js.intercomcdn.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://cdn.tiny.cloud",
            "img-src 'self' data: https: blob:",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://cdn.tiny.cloud",
            "connect-src 'self' https://www.google-analytics.com https://api.coingecko.com https://cdn.tiny.cloud https://api-iam.intercom.io https://nexus-websocket-a.intercom.io wss://nexus-websocket-a.intercom.io",
            "frame-src 'self' https://www.google.com https://www.youtube.com https://intercom-sheets.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
