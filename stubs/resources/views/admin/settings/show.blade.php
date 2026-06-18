<x-layouts.app title="Settings - {{ ucfirst($section) }}">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.index')" wire:navigate>Administration</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('admin.settings.index')" wire:navigate>Settings</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ ucfirst($section) }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <flux:heading size="xl" class="mb-6">{{ ucfirst($section) }} Settings</flux:heading>

    @livewire('admin.settings.show', ['section' => $section])
</x-layouts.app>
