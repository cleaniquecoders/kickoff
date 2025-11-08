<x-layouts.app title="Role Details">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('admin.index')" wire:navigate>Administration</flux:breadcrumbs.item>
                <flux:breadcrumbs.item :href="route('admin.roles.index')" wire:navigate>Roles</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Role Details</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <flux:heading size="xl" class="mb-6">Role Details</flux:heading>

        @livewire('admin.roles.show', ['uuid' => $uuid])
    </div>
</x-layouts.app>
