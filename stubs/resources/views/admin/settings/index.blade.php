<x-layouts.app title="Settings">
    <flux:heading size="xl" class="mb-6">Application Settings</flux:heading>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-globe class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">General</flux:heading>
                </div>
            </x-card.header>
            <x-card.body>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Manage your site name and general application settings.
                </p>
            </x-card.body>
            <x-card.footer>
                <flux:button variant="primary" :href="route('admin.settings.show', 'general')" wire:navigate>
                    Configure
                </flux:button>
            </x-card.footer>
        </x-card>

        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-lock-keyhole class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Authentication</flux:heading>
                </div>
            </x-card.header>
            <x-card.body>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Control how users sign in and whether they can register themselves.
                </p>
            </x-card.body>
            <x-card.footer>
                <flux:button variant="primary" :href="route('admin.settings.authentication')" wire:navigate>
                    Configure
                </flux:button>
            </x-card.footer>
        </x-card>

        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-mail class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Email</flux:heading>
                </div>
            </x-card.header>
            <x-card.body>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Configure the default sender address and name for outgoing emails.
                </p>
            </x-card.body>
            <x-card.footer>
                <flux:button variant="primary" :href="route('admin.settings.show', 'email')" wire:navigate>
                    Configure
                </flux:button>
            </x-card.footer>
        </x-card>

        <x-card>
            <x-card.header>
                <div class="flex items-center">
                    <x-lucide-bell class="h-6 w-6 text-brand-500 me-3" />
                    <flux:heading size="lg">Notifications</flux:heading>
                </div>
            </x-card.header>
            <x-card.body>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Enable or disable notifications and choose delivery channels.
                </p>
            </x-card.body>
            <x-card.footer>
                <flux:button variant="primary" :href="route('admin.settings.show', 'notifications')" wire:navigate>
                    Configure
                </flux:button>
            </x-card.footer>
        </x-card>
    </div>
</x-layouts.app>
