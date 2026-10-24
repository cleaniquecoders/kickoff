<?php

use Illuminate\Support\Facades\Artisan;

/*
| bin/deploy runs `php artisan optimize`. Feature tests only compile the views they render, so a
| view no test opens (a modal step, a vendor view) can be uncompilable and still ship green —
| until optimize fails on the server and leaves production in maintenance mode.
*/
it('compiles every view and caches every route like a deploy does', function () {
    try {
        expect(Artisan::call('view:cache'))->toBe(0, Artisan::output())
            ->and(Artisan::call('route:cache'))->toBe(0, Artisan::output());
    } finally {
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    }
});
