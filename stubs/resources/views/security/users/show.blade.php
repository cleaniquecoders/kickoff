<x-layouts.app :title="__('User Management')">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('User Management') }}</flux:heading>
            <flux:subheading size="lg">
                {{ __('Manage roles for :user', ['user' => $user->name]) }}
            </flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <livewire:security.user-roles :user="$user" />
    </div>
</x-layouts.app>
