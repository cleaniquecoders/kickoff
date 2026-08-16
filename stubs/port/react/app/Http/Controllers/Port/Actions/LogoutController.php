<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Actions;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Actions\Logout — review query/prop hydration before production use.
 */
class LogoutController
{
    public function __invoke(): Response
    {
        return Inertia::render('actions/logout', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Actions/Logout.php
            // []
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
