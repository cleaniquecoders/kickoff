<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class MailController extends Controller
{
    public function defaultMail()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('mail/DefaultMail', []);
    }

    public function notification()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('mail/Notification', []);
    }
}
