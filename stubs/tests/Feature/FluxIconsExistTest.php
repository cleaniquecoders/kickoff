<?php

use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Facades\File;

/*
| A Lucide icon must be imported (`php artisan flux:icon <name>`) before Flux can render it, and
| a missing one renders as nothing — no error, no failing test, just a blank space. Menu icons
| are PHP strings no view test ever renders. Fail here instead of in someone's browser.
*/
function fluxIconExists(string $name): bool
{
    return File::exists(resource_path("views/flux/icon/{$name}.blade.php"))
        || File::exists(base_path("vendor/livewire/flux/stubs/resources/views/flux/icon/{$name}.blade.php"));
}

it('uses only icons that exist in views and config', function () {
    $sources = collect(File::allFiles(resource_path('views')))
        ->reject(fn ($file) => str_contains($file->getPathname(), '/flux/icon/'))
        ->map(fn ($file) => [$file->getRelativePathname(), $file->getContents()])
        ->merge(collect(File::files(config_path()))->map(fn ($file) => [$file->getFilename(), $file->getContents()]));

    $missing = $sources->flatMap(function (array $source) {
        [$path, $content] = $source;
        preg_match_all('/\bicon(?::trailing|:leading)?="([a-z0-9-]+)"|<flux:icon\.([a-z0-9-]+)|\'icon\'\s*=>\s*\'([a-z0-9-]+)\'/', $content, $m);

        return collect([...$m[1], ...$m[2], ...$m[3]])
            ->filter()
            ->unique()
            ->reject(fn ($icon) => fluxIconExists($icon))
            ->map(fn ($icon) => "{$path}: {$icon}");
    })->values()->all();

    expect($missing)->toBe([]);
});

it('uses only icons that exist in the menus', function () {
    $this->seed(AccessControlSeeder::class);
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');
    $this->actingAs($superadmin);

    $icons = collect(['sidebar', 'administration', 'sidebar-footer'])
        ->flatMap(fn ($builder) => menu($builder)->menus()->map(fn ($item) => is_array($item) ? $item : $item->toArray()))
        ->pipe(function ($items) {
            $walk = fn ($items, $walk) => collect($items)->flatMap(
                fn ($item) => [($item['icon'] ?? null), ...$walk($item['children'] ?? [], $walk)]
            );

            return $walk($items, $walk);
        })
        ->filter()
        ->unique();

    expect($icons)->not->toBeEmpty()
        ->and($icons->reject(fn ($icon) => fluxIconExists($icon))->values()->all())->toBe([]);
});
