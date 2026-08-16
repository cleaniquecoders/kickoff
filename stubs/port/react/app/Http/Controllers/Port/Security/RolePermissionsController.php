<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Security;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Security\RolePermissions — review query/prop hydration before production use.
 */
class RolePermissionsController
{
    public function __invoke(): Response
    {
        return Inertia::render('security/role-permissions', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Security/RolePermissions.php
            // ['role', 'selectedPermissions']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}