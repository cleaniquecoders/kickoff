<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Notifications;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Notifications\Bell — review query/prop hydration before production use.
 */
class BellController
{
    public function __invoke(): Response
    {
        return Inertia::render('notifications/bell', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Notifications/Bell.php
            // []
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
