<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class SettingsController extends \App\Http\Controllers\Controller
{
    public function appearance()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/Appearance', []);
    }

    public function deleteUserForm()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/DeleteUserForm', []);
    }

    public function mcpTokens()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/McpTokens', []);
    }

    public function password()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/Password', []);
    }

    public function profile()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/Profile', []);
    }

    public function twoFactor()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/TwoFactor', []);
    }

    public function recoveryCodes()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('settings/twoFactor/RecoveryCodes', []);
    }

}
