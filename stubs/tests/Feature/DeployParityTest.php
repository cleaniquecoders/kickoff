<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

/*
| bin/deploy runs `php artisan optimize`. Feature tests only compile the views they render, so a
| view no test opens (a modal step, a vendor view) can be uncompilable and still ship green —
| until optimize fails on the server and leaves production in maintenance mode.
|
| These checks mirror view:cache / route:cache without writing the shared cache files, which
| would race with other workers under `pest --parallel`. CI runs the real `optimize`.
*/
it('compiles every app and package view like view:cache does', function () {
    $finder = View::getFinder();
    $paths = collect($finder->getPaths())->merge(collect($finder->getHints())->flatten())->unique()->filter(fn ($p) => is_dir($p));

    $failures = $paths
        ->flatMap(fn ($path) => File::allFiles($path))
        ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php'))
        ->map(function ($file) {
            try {
                Blade::compileString($file->getContents());

                return null;
            } catch (Throwable $e) {
                return $file->getPathname().': '.$e->getMessage();
            }
        })
        ->filter()
        ->values()
        ->all();

    expect($failures)->toBe([]);
});

it('has no duplicate route names (route:cache refuses them)', function () {
    $duplicates = collect(app('router')->getRoutes()->getRoutes())
        ->map->getName()
        ->filter()
        ->countBy()
        ->filter(fn ($count) => $count > 1)
        ->keys()
        ->all();

    expect($duplicates)->toBe([]);
});
