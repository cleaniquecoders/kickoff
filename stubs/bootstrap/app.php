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
    ->withMiddleware(function (Middleware $middleware) {
        // Trust the reverse proxy / tunnel (load balancer, ngrok, cloudflared) so
        // X-Forwarded-Proto is honoured. Without this, behind an HTTPS proxy that
        // forwards to plain-HTTP `php artisan serve`, request()->isSecure() is
        // false and Livewire/Flux emit http:// script URLs → mixed-content blocks.
        $middleware->trustProxies(at: '*');

        $middleware->append(SecurityHeaders::class);

        $middleware->appendToGroup('web', EnsureUserIsNotSuspended::class);

        // Written by JS for the sidebar rail toggle; must stay readable server-side.
        $middleware->encryptCookies(except: ['sidebar_collapsed']);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->throttleWithRedis();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
