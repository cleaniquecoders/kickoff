<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SecurityController extends Controller
{
    public function auditTrailIndex()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/auditTrail/Index', []);
    }

    public function rolePermissions()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/RolePermissions', []);
    }

    public function usersIndex()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/users/Index', []);
    }

    public function manageAccess()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/users/ManageAccess', []);
    }

    public function userForm()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/users/UserForm', []);
    }

    public function detail()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/auditTrail/Detail', []);
    }

    public function auditTrailShow()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('security/auditTrail/Show', []);
    }
}
