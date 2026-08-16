<?php

namespace App\Http\Controllers\Port;

use Inertia\Inertia;

class MailController extends \App\Http\Controllers\Controller
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
