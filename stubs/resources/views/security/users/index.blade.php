<x-layouts.app title="User Management">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <flux:breadcrumbs class="mb-6">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}">{{ __('Dashboard') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Users') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
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
    </div>
</x-layouts.app>
