<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class WelcomeController extends \App\Http\Controllers\Controller
{
    public function welcome()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('Welcome', []);
    }

}
