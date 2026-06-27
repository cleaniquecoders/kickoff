<x-layouts.app title="Settings - {{ ucfirst($section) }}">
    <x-breadcrumbs class="mb-6" :items="[
        ['label' => __('Administration'), 'url' => route('admin.index')],
        ['label' => __('Settings'), 'url' => route('admin.settings.index')],
        ['label' => ucfirst($section)],
    ]" />

    <flux:heading size="xl" class="mb-6">{{ ucfirst($section) }} Settings</flux:heading>

    @livewire('admin.settings.show', ['section' => $section])
</x-layouts.app>
