<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Security\Users;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Security\Users\ManageAccess — review query/prop hydration before production use.
 */
class ManageAccessController
{
    public function __invoke(): Response
    {
        return Inertia::render('security/users/manage-access', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Security/Users/ManageAccess.php
            // ['showing', 'userUuid']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
