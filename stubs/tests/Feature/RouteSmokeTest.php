<?php

use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

/*
| Every parameterless GET page, as superadmin, over non-empty tables. Catches the bugs a
| per-feature test never sees: a Blade compile error, a page only reached through the menu,
| an eager load that only breaks once a row exists, a route dropped by a baseline patch.
| Add to SKIP only with a reason.
*/
const SMOKE_SKIP_PREFIXES = [
    'livewire',       // Livewire internals
    'telescope',      // vendor dashboards, own assets/gates
    'horizon',
    'sanctum',
    'auth/sso',       // redirects to an external identity provider
    '.well-known',    // OAuth/MCP discovery documents
    'oauth',
    'mcp',
    'up',             // health route, covered by the framework
];

it('renders every page without a server error', function () {
    $this->seed(AccessControlSeeder::class);
    User::factory()->count(3)->create()->each->assignRole('user');

    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->filter(fn (Route $route) => in_array('GET', $route->methods(), true)
            && $route->getName() !== null
            && ! preg_match('/\{[^}?]+\}/', $route->uri())
            && ! Str::startsWith($route->uri(), SMOKE_SKIP_PREFIXES));

    expect($routes)->not->toBeEmpty();

    $failures = $routes
        ->map(function (Route $route) use ($superadmin) {
            $response = $this->actingAs($superadmin)->get(route($route->getName()));

            return $response->status() >= 500
                ? "{$route->getName()} → {$response->status()}: ".($response->exception?->getMessage() ?? '')
                : null;
        })
        ->filter()
        ->values()
        ->all();

    expect($failures)->toBe([]);
});
