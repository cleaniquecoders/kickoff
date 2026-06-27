<x-layouts.app title="User Management">
    <x-breadcrumbs class="mb-6" />
    <div class="flex items-end justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('User Management') }}</flux:heading>
            <flux:text class="mt-2">{{ $sub ?? __('Manage users in the application') }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            @can('create', App\Models\User::class)
                <flux:button variant="primary" icon="plus" class="cursor-pointer"
                    x-on:click="$dispatch('open-user-form')">
                    {{ __('Add User') }}
                </flux:button>
            @endcan
        </div>
    </div>
    <flux:separator variant="subtle" class="my-6" />

    @livewire('security.users.index')
    @livewire('security.users.user-form')
    @livewire('security.users.manage-access')
</x-layouts.app>
