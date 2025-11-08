<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('documentation', 'pages.documentation')->name('documentation');
    Route::view('support', 'pages.support')->name('support');
    Route::view('changelog', 'pages.changelog')->name('changelog');
});
