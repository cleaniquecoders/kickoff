<div>
    {{-- Full-page Livewire component: pin the route so the trail survives wire updates
         (request()->url() would be /livewire/update on re-render). --}}
    <x-breadcrumbs class="mb-6" for="admin.settings.g8desk" />

    <div class="flex items-end justify-between">
        <div>
            <flux:heading size="xl" level="1">Support</flux:heading>
            <flux:text class="mt-2">Embed the g8desk support widget so signed-in users can open tickets without leaving the app.</flux:text>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-6" />

    <form wire:submit="save" class="max-w-2xl">
        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-life-buoy class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Support Widget</flux:heading>
                </div>
            </x-card.header>
            <x-card.body class="space-y-6">
                <flux:field variant="inline">
                    <flux:switch wire:model="enabled" />
                    <flux:label>Enable the g8desk support widget</flux:label>
                </flux:field>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    No SDK, no npm package — when enabled, the widget loads directly from g8desk
                    for authenticated users on every app page (~5 lines of markup).
                </p>

                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400">
                    Need keys? In g8desk, open
                    <a href="{{ rtrim($baseUrl ?: 'https://g8desk.com', '/') }}/ops/organizations" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-500 hover:underline">Ops → your organisation → Intake Channels</a>,
                    create a channel, then <strong>Widget → Generate embed</strong> to reveal the public key and secret to paste below.
                </div>

                <div>
                    <flux:input wire:model="baseUrl" type="url" :label="__('Base URL')" placeholder="https://g8desk.com" />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The g8desk base URL. The widget script loads from <code>{base_url}/intake/widget.js</code>.
                    </p>
                </div>

                <div>
                    <flux:input wire:model="publicKey" :label="__('Public Key')" placeholder="pk_..." />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The pk_… data-key from g8desk → channel → Widget → Generate embed.
                    </p>
                </div>

                <div>
                    <flux:input wire:model="widgetSecret" type="password" :label="__('Widget Secret')" placeholder="g8wi_..." />
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        The g8wi_… identity secret; stored encrypted, never sent to the browser.
                    </p>
                </div>
            </x-card.body>
            <x-card.footer>
                <flux:button type="submit" variant="primary" icon="check" class="cursor-pointer">
                    Save changes
                </flux:button>
            </x-card.footer>
        </x-card>
    </form>
</div>
