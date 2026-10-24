<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

/**
 * Self-registration (Fortify's CreateNewUser) creates the user but assigns no role, so a
 * registered user could sign in and see nothing. Every other creation path already assigns
 * the default role; this closes the registration path.
 */
class AssignDefaultRole
{
    public function handle(Registered $event): void
    {
        $user = $event->user;
        $role = config('access-control.default_role', 'user');

        // Unseeded database (fresh install, a test without AccessControlSeeder): do not turn
        // a missing role into a failed registration.
        if (! $user instanceof User || $user->roles()->exists() || Role::where('name', $role)->doesntExist()) {
            return;
        }

        $user->assignRole($role);
    }
}
