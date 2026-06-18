<x-layouts.app title="Audit Details">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Audit Details</flux:heading>
            <flux:subheading>{{ $sub ?? 'View audit log details' }}</flux:subheading>
        </div>
        <flux:button variant="ghost" icon="arrow-left" href="{{ route('security.audit-trail.index') }}" class="cursor-pointer">
            Back
        </flux:button>
    </div>

    @include('security.audit-trail._detail', ['audit' => $audit])
</x-layouts.app>
