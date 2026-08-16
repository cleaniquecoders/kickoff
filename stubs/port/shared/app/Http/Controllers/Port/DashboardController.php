<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class DashboardController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('Dashboard', []);
    }

}
