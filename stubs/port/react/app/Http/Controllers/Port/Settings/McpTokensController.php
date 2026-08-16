<?php

declare(strict_types=1);

namespace App\Http\Controllers\Port\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Ported from App\Livewire\Settings\McpTokens — review query/prop hydration before production use.
 */
class McpTokensController
{
    public function __invoke(): Response
    {
        return Inertia::render('settings/mcp-tokens', [
            // public properties from the Livewire component become props
            // source: stubs/app/Livewire/Settings/McpTokens.php
            // ['plainTextToken', 'tokenName']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ported from Livewire $this->validate()
            'tokenName' => ['required', 'string', 'max:255']
        ]);

        // TODO: port the component's save/action methods here.
    }
}