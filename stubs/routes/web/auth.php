<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Settings routes for authenticated users (no email verification required for profile)
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'settings.profile')
        ->name('settings.profile.edit');
});

// Settings routes for verified users only
Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/password', 'settings.password')
        ->name('settings.user-password.edit');

    Route::livewire('settings/appearance', 'settings.appearance')
        ->name('settings.appearance.edit');

    Route::livewire('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('settings.two-factor.show');
});
