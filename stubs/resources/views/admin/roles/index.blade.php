<x-layouts.app title="Manage Roles">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <flux:heading size="xl">Roles Management</flux:heading>
            <flux:button variant="primary" icon="plus">
                Add New Role
            </flux:button>
        </div>

        @livewire('admin.roles.index')
    </div>
</x-layouts.app>
