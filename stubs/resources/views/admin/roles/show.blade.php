<x-layouts.app title="Role Details">
    <x-breadcrumbs class="mb-6" for="admin.roles.index" leaf="Role Details" />

    <flux:heading size="xl" class="mb-6">Role Details</flux:heading>

    @livewire('admin.roles.show', ['uuid' => $uuid])
</x-layouts.app>
