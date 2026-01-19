<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('notifications', 'notifications.index')->name('notifications.index');
});
