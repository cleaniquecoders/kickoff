<?php

use Illuminate\Support\Facades\Route;

// Telescope Routes
Route::middleware(['auth:sanctum', 'verified', 'can:access.telescope'])->group(function () {
    Route::redirect('/telescope', config('telescope.path', 'telescope'))->name('telescope');
});

// Horizon Routes
Route::middleware(['auth:sanctum', 'verified', 'can:access.horizon'])->group(function () {
    Route::redirect('/horizon', config('horizon.path', 'horizon'))->name('horizon.index');
});
