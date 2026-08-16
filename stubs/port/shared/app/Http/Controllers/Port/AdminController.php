<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class AdminController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/Index', []);
    }

    public function settingsIndex()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/settings/Index', []);
    }

    public function mailHistoryIndex()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/mailHistory/Index', []);
    }

    public function rolesIndex()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/roles/Index', []);
    }

    public function roleForm()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/roles/RoleForm', []);
    }

    public function rolesShow()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/roles/Show', []);
    }

    public function authentication()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/settings/Authentication', []);
    }

    public function g8desk()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/settings/G8desk', []);
    }

    public function seo()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/settings/Seo', []);
    }

    public function settingsShow()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('admin/settings/Show', []);
    }

}
