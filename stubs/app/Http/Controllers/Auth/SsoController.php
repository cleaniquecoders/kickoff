<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use CleaniqueCoders\ConfigSso\Http\Controllers\SsoController as BaseSsoController;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

/**
 * Hardens the package's email-matching sign-in (wired via config-sso.routes.controller).
 *
 * The package links an SSO identity to whichever local account has the same email. If that
 * account was never verified, whoever registered it may not own the address — they could have
 * pre-registered the victim's email and kept the password. The SSO login proves the real owner
 * controls the address, so the account is claimed: every credential the registrant set is
 * revoked before the owner is signed in. Users created here are marked verified via
 * forceFill, because email_verified_at is (correctly) not mass-assignable.
 */
class SsoController extends BaseSsoController
{
    /**
     * Replaces the package's lookup/creation rather than wrapping it: the package creates users
     * with create([... 'email_verified_at' => now()]), which a non-fillable column silently
     * drops (and Model::preventSilentlyDiscardingAttributes() turns into an exception).
     */
    protected function findOrCreateUser(object $socialiteUser): ?Authenticatable
    {
        $user = User::query()->where('email', $socialiteUser->getEmail())->first();

        if ($user) {
            if (! $user->hasVerifiedEmail()) {
                $this->claimUnverifiedAccount($user);
                $user->markEmailAsVerified();
            }

            return $user;
        }

        if (! config('config-sso.registration.enabled', true)) {
            return null;
        }

        $user = new User;
        $user->forceFill([
            'name' => $socialiteUser->getName() ?? $socialiteUser->getNickname() ?? 'SSO User',
            'email' => $socialiteUser->getEmail(),
            'password' => Str::password(64),
            'email_verified_at' => now(),
        ])->save();

        if ($role = config('config-sso.registration.default_role')) {
            $user->assignRole($role);
        }

        return $user;
    }

    // ponytail: sessions on the database/redis driver are not purged here; rotating the
    // password + remember token ends them only where AuthenticateSession is in use.
    protected function claimUnverifiedAccount(User $user): void
    {
        $user->forceFill([
            'password' => Str::password(64),
            'remember_token' => Str::random(60),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $user->tokens()->delete();
        $user->passkeys()->delete();
    }
}
