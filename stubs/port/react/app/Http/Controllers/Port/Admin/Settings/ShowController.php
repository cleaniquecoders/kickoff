<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Admin\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Admin\Settings\Show — review query/prop hydration before production use.
 */
class ShowController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/settings/show', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Admin/Settings/Show.php
            // ['section', 'settings', 'testRecipient']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ported from Livewire $this->validate()
            
                    'settings.general.site_name' => 'required|string|max:255',
                    'settings.general.timezone' => ['required', 'string', 'timezone'],
                
        ]);

        // TODO: port the component's save/action methods here.
    }
}