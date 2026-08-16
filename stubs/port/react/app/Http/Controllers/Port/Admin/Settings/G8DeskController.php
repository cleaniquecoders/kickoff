<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\Settings\G8Desk — review query/prop hydration before production use.
 */
class G8DeskController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/settings/g8-desk', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/Settings/G8Desk.php
            // ['baseUrl', 'enabled', 'publicKey', 'widgetSecret']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
