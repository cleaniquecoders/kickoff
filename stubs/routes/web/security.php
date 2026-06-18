<?php

use App\Http\Controllers\Security\AuditTrailController;
use App\Http\Controllers\Security\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'throttle:60,1'])->as('security.')->prefix('security')->group(function () {

    // User Management
    Route::get('users', [UserController::class, 'index'])
        ->middleware('can:manage.users')
        ->name('users.index');

    // Audit Trail
    Route::middleware(['can:view.audit-logs'])->group(function () {
        Route::get('audit-trail', [AuditTrailController::class, 'index'])
            ->name('audit-trail.index');
        Route::get('audit-trail/{uuid}', [AuditTrailController::class, 'show'])
            ->name('audit-trail.show');
    });
});
