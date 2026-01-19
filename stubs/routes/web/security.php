<?php

use App\Http\Controllers\Security\AuditTrailController;
use App\Http\Controllers\Security\UserController;
use Illuminate\Support\Facades\Route;

Route::as('security.')->prefix('security')->group(function () {

    // User Management
    Route::get('users', [UserController::class, 'index'])
        ->name('users.index');
    Route::get('users/{uuid}', [UserController::class, 'show'])
        ->name('users.show');

    // Audit Trail
    Route::get('audit-trail', [AuditTrailController::class, 'index'])
        ->name('audit-trail.index');
    Route::get('audit-trail/{uuid}', [AuditTrailController::class, 'show'])
        ->name('audit-trail.show');
});
