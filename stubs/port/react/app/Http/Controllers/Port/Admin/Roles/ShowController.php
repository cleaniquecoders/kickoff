<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\Roles;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\Roles\Show — review query/prop hydration before production use.
 */
class ShowController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/roles/show', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/Roles/Show.php
            // ['role', 'selectedPermissions']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}