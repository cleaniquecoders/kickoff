<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    /**
     * The application's default timezone, e.g. `UTC` or `Asia/Kuala_Lumpur`.
     * Must be a valid PHP timezone identifier. Applied over `config('app.timezone')`
     * at boot so all date/time functions use it.
     */
    public string $timezone;

    public static function group(): string
    {
        return 'general';
    }
}
