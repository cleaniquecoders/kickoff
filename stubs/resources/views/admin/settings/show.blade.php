<x-layouts.app title="Settings - {{ ucfirst($section) }}">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('admin.index')" wire:navigate>Administration</flux:breadcrumbs.item>
                <flux:breadcrumbs.item :href="route('admin.settings.index')" wire:navigate>Settings</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ ucfirst($section) }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <flux:heading size="xl" class="mb-6">{{ ucfirst($section) }} Settings</flux:heading>

        @livewire('admin.settings.show', ['section' => $section])
    </div>
</x-layouts.app>
