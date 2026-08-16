<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Notifications;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Notifications\Index — review query/prop hydration before production use.
 */
class NotificationsController
{
    public function __invoke(): Response
    {
        return Inertia::render('notifications/index', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Notifications/Index.php
            // ['filter', 'sortBy', 'sortDirection']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
