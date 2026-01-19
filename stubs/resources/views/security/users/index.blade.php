<x-layouts.app :title="__('User Management')">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('User Management') }}</flux:heading>
            <flux:subheading size="lg">{{ $sub }}</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <livewire:security.users.index />
    </div>
</x-layouts.app>
