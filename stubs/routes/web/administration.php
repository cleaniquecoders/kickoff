<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'can:access.admin-panel'])
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

            Route::get('settings/{section}', function ($section) {
                return view('admin.settings.show', compact('section'));
            })->name('settings.show');
        });

    });
