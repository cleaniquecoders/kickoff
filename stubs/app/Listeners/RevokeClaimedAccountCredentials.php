<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use CleaniqueCoders\ConfigSso\Events\UnverifiedAccountClaimed;

/**
 * An SSO sign-in matched an unverified local account by email. laravel-config-sso has already
 * rotated its password and remember token and marked the email verified; revoke the rest of what
 * the registrant could have set up, so whoever pre-registered the address keeps no way in.
 */
class RevokeClaimedAccountCredentials
{
    // ponytail: sessions on the database/redis driver are not purged here; the password and
    // remember-token rotation ends them only where AuthenticateSession is in use.
    public function handle(UnverifiedAccountClaimed $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $user->tokens()->delete();
        $user->passkeys()->delete();
    }
}
