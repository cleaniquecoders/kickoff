<x-layouts.app :title="__('Access Control')">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Access Control') }}</flux:heading>
            <flux:subheading size="lg">{{ $sub }}</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <livewire:security.access-control.index />
    </div>
</x-layouts.app>
