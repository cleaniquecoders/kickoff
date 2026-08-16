<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('Dashboard', []);
    }
}
