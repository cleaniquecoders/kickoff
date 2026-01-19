<x-layouts.app :title="__('Access Control')">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Access Control') }}</flux:heading>
            <flux:subheading size="lg">
                {{ __('Manage permissions for :role', ['role' => $role->display_name]) }}
            </flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <livewire:security.role-permissions :role="$role" />
    </div>
</x-layouts.app>
