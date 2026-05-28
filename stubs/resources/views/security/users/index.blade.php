<x-layouts.app title="User Management">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <flux:heading size="xl">User Management</flux:heading>
                <flux:subheading>{{ $sub ?? 'Manage users in the application' }}</flux:subheading>
            </div>
        </div>

        <livewire:security.user-index />
    </div>
</x-layouts.app>
