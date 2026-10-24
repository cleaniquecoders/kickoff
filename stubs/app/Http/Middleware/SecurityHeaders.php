<?php

namespace App\Http\Middleware;

use App\Settings\G8DeskSettings;
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
            $response->headers->set('Content-Security-Policy', $this->policy());
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

    /**
     * The configured policy plus the third-party origins Kickoff's own views load.
     *
     * A bare 'self' policy silently blocks the template's fonts, the GA/GTM snippets an
     * admin switches on in SEO settings, and the g8desk widget — in production only, with
     * nothing but a browser-console error. Anything a view loads from another origin must
     * be added here, gated on the same setting that renders it.
     */
    private function policy(): string
    {
        $policy = (string) config('security.headers.csp_policy');

        foreach ($this->thirdPartySources() as $directive => $origins) {
            $policy = $this->allow($policy, $directive, $origins);
        }

        return $policy;
    }

    /**
     * @return array<string, list<string>>
     */
    private function thirdPartySources(): array
    {
        $sources = [
            'style-src' => ['https://fonts.bunny.net'],
            'font-src' => ['https://fonts.bunny.net'],
            // cleaniquecoders/media-manager's published media-collection view loads
            // SortableJS from jsDelivr. Drop this once the package bundles it.
            'script-src' => ['https://cdn.jsdelivr.net'],
        ];

        if (config('seo.google.tag_manager_id') || config('seo.google.analytics_id')) {
            $google = ['https://www.googletagmanager.com', 'https://*.google-analytics.com', 'https://*.analytics.google.com'];

            foreach (['script-src', 'img-src', 'connect-src', 'frame-src'] as $directive) {
                $sources[$directive] = [...($sources[$directive] ?? []), ...$google];
            }
        }

        $g8desk = rescue(fn () => app(G8DeskSettings::class), null, false);

        if ($g8desk?->enabled && $g8desk->base_url !== '') {
            $origin = rtrim($g8desk->base_url, '/');

            foreach (['script-src', 'img-src', 'connect-src', 'frame-src'] as $directive) {
                $sources[$directive] = [...($sources[$directive] ?? []), $origin];
            }
        }

        return $sources;
    }

    /**
     * @param  list<string>  $origins
     */
    private function allow(string $policy, string $directive, array $origins): string
    {
        $origins = implode(' ', array_unique($origins));
        $pattern = '/(^|;)\s*'.preg_quote($directive, '/').'\b([^;]*)/';

        if (preg_match($pattern, $policy)) {
            return (string) preg_replace($pattern, '$1 '.$directive.'$2 '.$origins, $policy, 1);
        }

        return rtrim($policy, '; ')."; {$directive} 'self' {$origins}";
    }
}
