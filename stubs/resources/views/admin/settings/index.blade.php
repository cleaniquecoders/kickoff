<x-layouts.app title="Settings">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <flux:heading size="xl" class="mb-6">Application Settings</flux:heading>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <flux:card>
                <flux:card.header>
                    <div class="flex items-center">
                        <x-lucide-globe class="h-6 w-6 text-brand-500 me-3" />
                        <flux:heading size="lg">General</flux:heading>
                    </div>
                </flux:card.header>
                <flux:card.body>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        General application settings
                    </p>
                </flux:card.body>
                <flux:card.footer>
                    <flux:button variant="primary" :href="route('admin.settings.show', 'general')" wire:navigate>
                        Configure
                    </flux:button>
                </flux:card.footer>
            </flux:card>

            <flux:card>
                <flux:card.header>
                    <div class="flex items-center">
                        <x-lucide-mail class="h-6 w-6 text-brand-500 me-3" />
                        <flux:heading size="lg">Email</flux:heading>
                    </div>
                </flux:card.header>
                <flux:card.body>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Email configuration and templates
                    </p>
                </flux:card.body>
                <flux:card.footer>
                    <flux:button variant="primary" :href="route('admin.settings.show', 'email')" wire:navigate>
                        Configure
                    </flux:button>
                </flux:card.footer>
            </flux:card>

            <flux:card>
                <flux:card.header>
                    <div class="flex items-center">
                        <x-lucide-bell class="h-6 w-6 text-brand-500 me-3" />
                        <flux:heading size="lg">Notifications</flux:heading>
                    </div>
                </flux:card.header>
                <flux:card.body>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Notification preferences
                    </p>
                </flux:card.body>
                <flux:card.footer>
                    <flux:button variant="primary" :href="route('admin.settings.show', 'notifications')" wire:navigate>
                        Configure
                    </flux:button>
                </flux:card.footer>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
