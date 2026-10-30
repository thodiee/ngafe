<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach strong security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Anti-Clickjacking: prevent framing on external origins
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Anti-MIME Sniffing: prevent browser from executing non-script as script
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Legacy XSS filter activation
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy: prevent leaking sensitive parameters to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict device permissions (allow camera and geolocation for face attendance only on self)
        $response->headers->set(
            'Permissions-Policy',
            'camera=(self), geolocation=(self), microphone=(), payment=(self)'
        );

        // Content Security Policy (CSP) tailored for the application
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://app.midtrans.com https://app.sandbox.midtrans.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: blob: https://images.unsplash.com https://api.qrserver.com https://app.midtrans.com https://app.sandbox.midtrans.com",
            "connect-src 'self' https://app.midtrans.com https://app.sandbox.midtrans.com https://api.qrserver.com",
            "frame-src 'self' https://app.midtrans.com https://app.sandbox.midtrans.com",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // HSTS (HTTP Strict Transport Security) when running on HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
