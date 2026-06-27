<x-layouts.app :title="__('Notifications')">
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Notifications') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="mb-6">
        <flux:heading size="xl" level="1">
            <x-icon name="bell" class="inline-block h-8 w-8 mr-2" />
            {{ __('Notifications') }}
        </flux:heading>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Manage your notifications and stay up to date.') }}
        </p>
    </div>

    <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
        @livewire('notifications.index')
    </div>
</x-layouts.app>
