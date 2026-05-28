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

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($this->cspEnabled()) {
            $response->headers->set('Content-Security-Policy', (string) config('security.headers.csp_policy'));
        }

        return $response;
    }

    /**
     * CSP is auto-enabled in production (where assets are served from origin)
     * and off in local/dev (the Vite dev server + HMR would be blocked). The
     * SECURITY_CSP env flag forces it on/off in any environment.
     */
    private function cspEnabled(): bool
    {
        $configured = config('security.headers.csp');

        if ($configured === null) {
            return app()->environment('production');
        }

        return filter_var($configured, FILTER_VALIDATE_BOOLEAN);
    }
}
