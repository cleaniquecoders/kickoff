<?php

use App\Listeners\LogoutFromOtherDevices;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Event;

it('registers each listener exactly once', function () {
    $listeners = collect(Event::getRawListeners()[Authenticated::class] ?? [])
        ->map(fn ($listener) => is_string($listener) ? explode('@', $listener)[0] : $listener)
        ->filter(fn ($listener) => $listener === LogoutFromOtherDevices::class);

    expect($listeners)->toHaveCount(1);
});
