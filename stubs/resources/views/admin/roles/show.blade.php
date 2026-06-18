<x-layouts.app title="Role Details">
    <div class="mb-6">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('admin.index')" wire:navigate>Administration</flux:breadcrumbs.item>
            <flux:breadcrumbs.item :href="route('admin.roles.index')" wire:navigate>Roles</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Role Details</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <flux:heading size="xl" class="mb-6">Role Details</flux:heading>

    @livewire('admin.roles.show', ['uuid' => $uuid])
</x-layouts.app>
