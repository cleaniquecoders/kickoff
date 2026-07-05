<?php

use App\Livewire\Admin\Settings\Authentication;
use App\Livewire\Admin\Settings\G8Desk;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'throttle:60,1', 'can:access.admin-panel'])
    ->as('admin.')
    ->prefix('admin')
    ->group(function () {

        Route::view('/', 'admin.index')->name('index');

        // Roles Management
        Route::middleware(['can:manage.roles'])->group(function () {
            Route::get('roles', function () {
                return view('admin.roles.index');
            })->name('roles.index');

            Route::get('roles/{uuid}', function ($uuid) {
                return view('admin.roles.show', compact('uuid'));
            })->name('roles.show');
        });

        // Settings Management
        Route::middleware(['can:manage.settings'])->group(function () {
            Route::get('settings', function () {
                return view('admin.settings.index');
            })->name('settings.index');

            // Declared before the {section} catch-all so it isn't swallowed by it.
            Route::get('settings/authentication', Authentication::class)
                ->name('settings.authentication');

            Route::get('settings/g8desk', G8Desk::class)
                ->name('settings.g8desk');

            Route::get('settings/{section}', function ($section) {
                return view('admin.settings.show', compact('section'));
            })->name('settings.show');
        });

        // Mail History — outbound email audit log (cleaniquecoders/mailhistory)
        Route::middleware(['can:admin.view.mail-history'])->group(function () {
            Route::get('mail-history', function () {
                return view('admin.mail-history.index');
            })->name('mail-history.index');
        });

    });
