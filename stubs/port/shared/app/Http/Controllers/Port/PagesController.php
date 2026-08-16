<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class PagesController extends \App\Http\Controllers\Controller
{
    public function changelog()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('pages/Changelog', []);
    }

    public function documentation()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('pages/Documentation', []);
    }

    public function support()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('pages/Support', []);
    }

}
