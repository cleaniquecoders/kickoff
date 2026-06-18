<x-layouts.app title="Manage Roles">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <flux:breadcrumbs class="mb-6">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}">{{ __('Dashboard') }}</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Roles') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <div class="flex items-end justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Roles Management') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Manage roles and their permissions.') }}</flux:text>
            </div>
            <div class="flex items-center gap-2">
                @can('create', App\Models\Role::class)
                    <flux:button variant="primary" icon="plus" class="cursor-pointer"
                        x-on:click="$dispatch('open-role-form')">
                        {{ __('Add Role') }}
                    </flux:button>
                @endcan
            </div>
        </div>
        <flux:separator variant="subtle" class="my-6" />

        @livewire('admin.roles.index')
        @livewire('admin.roles.role-form')
    </div>
</x-layouts.app>
