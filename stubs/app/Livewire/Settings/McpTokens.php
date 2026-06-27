<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Self-service MCP token management (settings sidebar page).
 *
 * Token-only (Sanctum) transport: issues personal access tokens for
 * header-capable AI clients (Claude Code, Claude Desktop). The "Connected
 * Apps" section is reserved for an OAuth (Passport) transport and shows an
 * empty state until that transport is wired up.
 *
 * Adapted from the laravel-mcp-kit `mcp-kit-ui` stub to live inside the
 * shared settings layout and match the app's card/table styling.
 */
#[Layout('components.layouts.app')]
#[Title('MCP Tokens')]
class McpTokens extends Component
{
    public string $tokenName = '';

    /** The plaintext token, shown exactly once right after creation. */
    public ?string $plainTextToken = null;

    public function mount(): void
    {
        abort_unless(config('mcp-kit.enabled', true), 404);

        abort_unless(
            auth()->user()?->canAny([
                config('mcp-kit.abilities.view-tasks', 'mcp-kit.view-tasks'),
                config('mcp-kit.abilities.manage-tasks', 'mcp-kit.manage-tasks'),
            ]),
            403,
        );
    }

    /** @return Collection<int, PersonalAccessToken> */
    #[Computed]
    public function tokens(): Collection
    {
        return auth()->user()->tokens()->latest()->get();
    }

    /**
     * OAuth-connected apps. Empty under the token-only (Sanctum) transport;
     * populated once an OAuth (Passport) transport is installed.
     *
     * @return Collection<int, mixed>
     */
    #[Computed]
    public function connectedApps(): Collection
    {
        return collect();
    }

    public function createToken(): void
    {
        $this->validate(['tokenName' => ['required', 'string', 'max:255']]);

        $this->plainTextToken = auth()->user()
            ->createToken($this->tokenName)
            ->plainTextToken;

        $this->reset('tokenName');
        unset($this->tokens);

        $this->dispatch('toast', type: 'success', message: __('Token generated. Copy it now — it will not be shown again.'));
    }

    public function revoke(string $id): void
    {
        auth()->user()->tokens()->whereKey($id)->delete();
        unset($this->tokens);

        $this->dispatch('toast', type: 'success', message: __('Token revoked.'));
    }

    public function render(): View
    {
        return view('livewire.settings.mcp-tokens');
    }
}
