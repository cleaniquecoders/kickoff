<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'can:access.media-management'])
    ->prefix('media-manager')
    ->as('media-manager.')
    ->group(function () {
        Route::get('/', function () {
            return view('media-manager::browser');
        })->name('index');
    });
