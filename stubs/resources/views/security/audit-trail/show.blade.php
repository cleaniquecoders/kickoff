<x-layouts.app title="Audit Details">
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('security.audit-trail.index')" wire:navigate>{{ __('Audit Trail') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Details') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">Audit Details</flux:heading>
            <flux:subheading>{{ $sub ?? 'View audit log details' }}</flux:subheading>
        </div>
        <flux:button variant="ghost" icon="arrow-left" href="{{ route('security.audit-trail.index') }}" class="cursor-pointer">
            Back
        </flux:button>
    </div>

    @include('security.audit-trail._detail', ['audit' => $audit])
</x-layouts.app>
