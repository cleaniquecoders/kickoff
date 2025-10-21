<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'can:access.admin-panel'])
    ->as('administration.')
    ->prefix('administration')
    ->group(function () {

        Route::view('/', 'administration.index')->name('index');

    });
