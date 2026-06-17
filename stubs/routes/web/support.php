<?php

// Telescope and Horizon register their own dashboard routes with stable names
// when enabled:
//   - Telescope  -> name('telescope')      (only when TELESCOPE_ENABLED=true)
//   - Horizon    -> name('horizon.index')  (always)
//
// We deliberately do NOT add redirect routes here. A `Route::redirect('/telescope',
// config('telescope.path'))` self-loops (source path === target path === 'telescope')
// and duplicates the vendor route name, producing ERR_TOO_MANY_REDIRECTS — especially
// when Telescope is disabled and its real route never registers. The Audit & Monitoring
// menu links to these vendor route names directly and guards them with Route::has().
