<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\Roles;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\Roles\RoleForm — review query/prop hydration before production use.
 */
class RoleFormController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/roles/role-form', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/Roles/RoleForm.php
            // ['description', 'displayName', 'editingUuid', 'isEnabled', 'isProtected', 'showing']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ported from Livewire $this->validate()
            
            'displayName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        
        ]);

        // TODO: port the component's save/action methods here.
    }
}