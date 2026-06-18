<x-layouts.app title="Audit Trail">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Audit Trail</flux:heading>
            <flux:subheading>{{ $sub ?? 'View activity logs and changes' }}</flux:subheading>
        </div>
    </div>

    @livewire('security.audit-trail.index')
</x-layouts.app>
