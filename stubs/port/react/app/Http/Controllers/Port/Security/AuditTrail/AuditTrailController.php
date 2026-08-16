<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Security\AuditTrail;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Security\AuditTrail\Index — review query/prop hydration before production use.
 */
class AuditTrailController
{
    public function __invoke(): Response
    {
        return Inertia::render('security/audit-trail/index', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Security/AuditTrail/Index.php
            // ['detailKey', 'detailUuid', 'showDetail']
        ]);
    }

    public function store(Request $request)
    {
        // no validation rules on the source component

        // TODO: port the component's save/action methods here.
    }
}