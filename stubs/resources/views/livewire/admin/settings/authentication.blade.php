<div>
    {{-- Full-page Livewire component: pin the route so the trail survives wire updates
         (request()->url() would be /livewire/update on re-render). --}}
    <x-breadcrumbs class="mb-6" for="admin.settings.authentication" />

    <div class="flex items-end justify-between">
        <div>
            <flux:heading size="xl" level="1">Authentication</flux:heading>
            <flux:text class="mt-2">Control how users sign in and whether they can register themselves.</flux:text>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-6" />

    <form wire:submit="save" class="max-w-2xl">
        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-user-plus class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Registration</flux:heading>
                </div>
            </x-card.header>
            <x-card.body class="space-y-4">
                <flux:field variant="inline">
                    <flux:checkbox wire:model="publicRegistrationEnabled" />
                    <flux:label>Allow public registration</flux:label>
                </flux:field>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    When enabled, anyone can create an account from the public sign-up page.
                    When disabled, the registration page is unavailable and only administrators
                    can add users.
                </p>
            </x-card.body>
            <x-card.footer>
                <flux:button type="submit" variant="primary" icon="check" class="cursor-pointer">
                    Save changes
                </flux:button>
            </x-card.footer>
        </x-card>
    </form>
</div>
