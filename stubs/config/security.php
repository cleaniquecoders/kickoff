<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    |
    | Configure password validation rules applied globally via
    | Password::defaults() in AppServiceProvider.
    |
    */

    'password' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 12),
        'require_mixed_case' => env('PASSWORD_REQUIRE_MIXED_CASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', true),
        'require_uncompromised' => env('PASSWORD_REQUIRE_UNCOMPROMISED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */

    'session' => [
        'encrypt' => env('SESSION_ENCRYPT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Content-Security-Policy applied by App\Http\Middleware\SecurityHeaders.
    | CSP is auto-enabled in production only — local/dev serves assets via the
    | Vite dev server (different origin + ws HMR), which a 'self' policy would
    | break. Set SECURITY_CSP=1 / 0 to force it on/off in any environment
    | (e.g. to verify in staging).
    |
    | The default policy keeps 'unsafe-inline'/'unsafe-eval' for scripts and
    | styles because Livewire injects inline scripts and Alpine evaluates
    | expressions at runtime; it still locks down object-src, base-uri,
    | frame-ancestors and form-action.
    |
    */

    'headers' => [
        'csp' => env('SECURITY_CSP', null), // null = auto (production only)
        'csp_policy' => env(
            'SECURITY_CSP_POLICY',
            "default-src 'self'; ".
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'; ".
            "style-src 'self' 'unsafe-inline'; ".
            "img-src 'self' data: blob:; ".
            "font-src 'self' data:; ".
            "connect-src 'self'; ".
            "object-src 'none'; ".
            "base-uri 'self'; ".
            "frame-ancestors 'none'; ".
            "form-action 'self'"
        ),
    ],

];
