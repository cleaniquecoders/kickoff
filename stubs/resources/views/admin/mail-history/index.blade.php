<x-layouts.app title="Mail History">
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('admin.index')" wire:navigate>Administration</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Mail History</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex items-end justify-between">
        <div>
            <flux:heading size="xl" level="1">Mail History</flux:heading>
            <flux:text class="mt-2">Audit log of every email sent by the application, with delivery status and event timeline.</flux:text>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-6" />

    @livewire('admin.mail-history.index')
</x-layouts.app>
