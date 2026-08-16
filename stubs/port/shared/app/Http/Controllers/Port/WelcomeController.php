<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function welcome()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('Welcome', []);
    }
}
