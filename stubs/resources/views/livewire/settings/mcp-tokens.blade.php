<div>
    {{-- Full-page Livewire component: pin the route so the trail survives wire updates
         (request()->url() would be /livewire/update on re-render). --}}
    <x-breadcrumbs class="mb-6" for="settings.mcp-tokens.show" />

    <div class="flex items-end justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('MCP Tokens') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Connect AI clients (Claude Code, Claude Desktop) to the MCP server.') }}</flux:text>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-6" />

    <div class="space-y-6">
        {{-- One-time token display --}}
        @if ($plainTextToken)
            <flux:callout variant="success" icon="key">
                <flux:callout.heading>{{ __('Token created — copy it now') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('This is the only time the token is shown.') }}
                    <flux:input readonly copyable value="{{ $plainTextToken }}" class="mt-2" />
                    <p class="mt-3 text-sm">{{ __('Add it to Claude:') }}</p>
                    <flux:input readonly copyable class="mt-1"
                        value="claude mcp add --transport http mcp-kit {{ route('mcp-kit.tasks') }} --header &quot;Authorization: Bearer {{ $plainTextToken }}&quot;" />
                </flux:callout.text>
            </flux:callout>
        @endif

        {{-- Generate a new token --}}
        <x-card>
            <div class="p-6">
                <flux:heading size="lg">{{ __('Token name') }}</flux:heading>
                <flux:text class="mt-1">{{ __('A label to recognise where this token is used.') }}</flux:text>

                <form wire:submit="createToken" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="flex-1">
                        <flux:input
                            wire:model="tokenName"
                            placeholder="{{ __('e.g. claude-code-laptop') }}"
                        />
                    </div>
                    <flux:button type="submit" variant="primary" icon="key" class="cursor-pointer">
                        {{ __('Generate Token') }}
                    </flux:button>
                </form>
            </div>
        </x-card>

        {{-- Active tokens --}}
        <x-card>
            <div class="p-6">
                <flux:heading size="lg">{{ __('Personal Access Tokens') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Tokens you have generated for header-capable AI clients.') }}</flux:text>

                <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->tokens as $token)
                        <div wire:key="token-{{ $token->id }}" class="flex items-center justify-between gap-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $token->name }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('Created :when', ['when' => $token->created_at->diffForHumans()]) }}
                                    · {{ __('Last used :when', ['when' => $token->last_used_at?->diffForHumans() ?? __('never')]) }}
                                </p>
                            </div>
                            <flux:button
                                size="sm"
                                variant="danger"
                                icon="trash-2"
                                class="cursor-pointer"
                                wire:click="revoke('{{ $token->id }}')"
                                wire:confirm="{{ __('Revoke this token? Clients using it will lose access.') }}"
                            >
                                {{ __('Revoke') }}
                            </flux:button>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center gap-2 py-10 text-center">
                            <flux:icon.key class="size-8 text-zinc-400 dark:text-zinc-500" />
                            <flux:text class="text-zinc-500 dark:text-zinc-400">
                                {{ __('No tokens yet. Generate one above to connect an AI client.') }}
                            </flux:text>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-card>

        {{-- OAuth connected apps --}}
        <x-card>
            <div class="p-6">
                <flux:heading size="lg">{{ __('Connected Apps') }}</flux:heading>
                <flux:text class="mt-1">
                    {{ __('AI clients connected via OAuth (e.g. Claude on claude.ai). They authorize through your login — no token paste needed.') }}
                </flux:text>

                <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->connectedApps as $app)
                        <div wire:key="app-{{ $app->id }}" class="flex items-center justify-between gap-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $app->client->name ?? __('Unknown') }}</p>
                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('Connected :when', ['when' => $app->created_at->diffForHumans()]) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <flux:text class="block py-4 text-zinc-500 dark:text-zinc-400">
                            {{ __('No connected apps. In Claude, add a custom connector pointing to the MCP endpoint and authorize it.') }}
                        </flux:text>
                    @endforelse
                </div>
            </div>
        </x-card>
    </div>
</div>
