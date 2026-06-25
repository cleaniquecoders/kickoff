<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * DB-stored authentication configuration.
 *
 * `AppServiceProvider::boot()` lays these over `config('admin.*')` so the
 * login screen and Fortify read them through normal `config()` calls. `.env`
 * keeps the fresh-install defaults (seeded by the settings migration).
 */
class AuthenticationSettings extends Settings
{
    /**
     * Whether the public self-service registration page is available.
     * When false, the registration routes are not registered and only
     * administrators can create accounts.
     */
    public bool $public_registration_enabled;

    public static function group(): string
    {
        return 'authentication';
    }
}
