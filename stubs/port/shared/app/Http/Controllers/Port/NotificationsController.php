<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class NotificationsController extends Controller
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
