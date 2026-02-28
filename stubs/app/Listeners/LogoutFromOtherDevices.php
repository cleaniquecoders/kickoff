<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;

class LogoutFromOtherDevices
{
    /**
     * Handle the event.
     */
    public function handle(Authenticated $event): void
    {
        if (config('auth.single-device') && request()->has('password') && in_array('logoutOtherDevices', get_class_methods(Auth::class))) {
            Auth::logoutOtherDevices(request('password'));
        }
    }
}
