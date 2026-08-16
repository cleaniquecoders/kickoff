<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Security\Users;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Security\Users\UserForm — review query/prop hydration before production use.
 */
class UserFormController
{
    public function __invoke(): Response
    {
        return Inertia::render('security/users/user-form', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Security/Users/UserForm.php
            // ['editingUuid', 'email', 'name', 'roles', 'sendPasswordSetupLink', 'showing']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ported from Livewire $this->validate()
            
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($editing?->id),
            ],
            'roles' => ['array'],
            'roles.*' => [Rule::in($this->assignableRoles->pluck('name'))],
        
        ]);

        // TODO: port the component's save/action methods here.
    }
}