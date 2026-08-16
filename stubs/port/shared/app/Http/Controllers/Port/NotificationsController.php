<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class NotificationsController extends \App\Http\Controllers\Controller
{
    public function bell()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('notifications/Bell', []);
    }

    public function index()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('notifications/Index', []);
    }

}
