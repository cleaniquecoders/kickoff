<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\Settings\Authentication — review query/prop hydration before production use.
 */
class AuthenticationController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/settings/authentication', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/Settings/Authentication.php
            // ['publicRegistrationEnabled']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
