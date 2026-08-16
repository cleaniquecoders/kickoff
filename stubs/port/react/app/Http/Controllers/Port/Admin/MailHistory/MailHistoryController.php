<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\MailHistory;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\MailHistory\Index — review query/prop hydration before production use.
 */
class MailHistoryController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/mail-history/index', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/MailHistory/Index.php
            // ['perPage', 'search', 'showDetail', 'status', 'viewingUuid']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}
