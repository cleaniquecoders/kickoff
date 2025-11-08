<x-layouts.app title="Administration">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <flux:heading size="xl" class="mb-6">Administration</flux:heading>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @can('manage.roles')
                    <flux:card>
                        <flux:card.header>
                            <div class="flex items-center">
                                <x-lucide-shield-check class="h-6 w-6 text-brand-500 me-3" />
                                <flux:heading size="lg">Roles</flux:heading>
                            </div>
                        </flux:card.header>
                        <flux:card.body>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Manage user roles and permissions
                            </p>
                        </flux:card.body>
                        <flux:card.footer>
                            <flux:button variant="primary" :href="route('admin.roles.index')" wire:navigate>
                                Manage Roles
                            </flux:button>
                        </flux:card.footer>
                    </flux:card>
                @endcan

                <flux:card>
                    <flux:card.header>
                        <div class="flex items-center">
                            <x-lucide-settings class="h-6 w-6 text-brand-500 me-3" />
                            <flux:heading size="lg">Settings</flux:heading>
                        </div>
                    </flux:card.header>
                    <flux:card.body>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Configure application settings
                        </p>
                    </flux:card.body>
                    <flux:card.footer>
                        <flux:button variant="primary" :href="route('admin.settings.index')" wire:navigate>
                            Manage Settings
                        </flux:button>
                    </flux:card.footer>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts.app>
