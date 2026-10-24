<?php

use App\Http\Middleware\EnsureUserIsNotSuspended;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Listeners are registered explicitly in App\Providers\EventServiceProvider::$listen.
    // Discovery must be switched off HERE: the framework's own event provider scans
    // app/Listeners regardless of shouldDiscoverEvents() on ours, so every listener
    // would otherwise be registered twice (and every listener-sent email go out twice).
    ->withEvents(discover: false)
    ->withMiddleware(function (Middleware $middleware) {
        // Trusted proxies come from config/trustedproxy.php (TRUSTED_PROXIES),
        // read per request by the framework's TrustProxies middleware. Do not call
        // trustProxies(at: '*') here: it applies in production too, letting any
        // client spoof X-Forwarded-For past IP-keyed throttles and audit logs.

        $middleware->append(SecurityHeaders::class);

        $middleware->appendToGroup('web', EnsureUserIsNotSuspended::class);

        // Written by JS for the sidebar rail toggle; must stay readable server-side.
        $middleware->encryptCookies(except: ['sidebar_collapsed']);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // Redis-backed throttling is shared state across processes — in tests it
        // ignores the array cache override, so rate-limit hits leak between runs
        // (TIA re-runs the same tests in tight loops and gets 429s). Keep the
        // default cache-based throttle in the testing environment. Read the process
        // env directly: this callback runs before the container's env binding
        // exists, and env() is reserved for config/ by the architecture tests.
        if (($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) !== 'testing') {
            $middleware->throttleWithRedis();
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
