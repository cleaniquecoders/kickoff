<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Security\Users;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Security\Users\Index — review query/prop hydration before production use.
 */
class UsersController
{
    public function __invoke(): Response
    {
        return Inertia::render('security/users/index', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Security/Users/Index.php
            // ['bulkRole', 'detailKey', 'detailUuid', 'roleFilter', 'search', 'selectPage', 'selected', 'showDetail', 'statusFilter']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ported from Livewire $this->validate()
            
            'bulkRole' => ['required', 'exists:roles,name'],
        
        ]);

        // TODO: port the component's save/action methods here.
    }
}