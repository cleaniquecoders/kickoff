<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled maintenance
|--------------------------------------------------------------------------
| Retention and pruning the docs promise only happen if they are scheduled
| here — a purge command nobody runs is not a control. Production needs one
| cron entry: `* * * * * cd /var/www/app && php artisan schedule:run`.
| Retention windows come from config/audit.php (data:purge) and each
| package's config; keep them there rather than as flags here.
*/

Schedule::command('data:purge')->daily()->withoutOverlapping()->onOneServer();
Schedule::command('mailhistory:prune')->daily()->withoutOverlapping()->onOneServer();
Schedule::command('config-webhook:prune --days=30')->daily()->withoutOverlapping()->onOneServer();
Schedule::command('config-backup:prune')->daily()->withoutOverlapping()->onOneServer();
Schedule::command('queue:prune-failed --hours=168')->daily()->onOneServer();
Schedule::command('queue:prune-batches --hours=168')->daily()->onOneServer();
Schedule::command('sanctum:prune-expired --hours=24')->daily()->onOneServer();
Schedule::command('horizon:snapshot')->everyFiveMinutes()->onOneServer();
