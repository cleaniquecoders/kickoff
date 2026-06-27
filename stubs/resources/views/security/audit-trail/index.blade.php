<x-layouts.app title="Audit Trail">
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Audit Trail') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Audit Trail</flux:heading>
            <flux:subheading>{{ $sub ?? 'View activity logs and changes' }}</flux:subheading>
        </div>
    </div>

    @livewire('security.audit-trail.index')
</x-layouts.app>
