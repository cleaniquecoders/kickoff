<div>
    {{-- Stats and Actions --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <flux:badge color="zinc">
                {{ __('Total') }}: {{ $this->totalCount }}
            </flux:badge>
            <flux:badge color="amber">
                {{ __('Unread') }}: {{ $this->unreadCount }}
            </flux:badge>
        </div>

        @if ($this->unreadCount > 0)
            <flux:button variant="ghost" size="sm" wire:click="markAllAsRead" icon="check">
                {{ __('Mark All as Read') }}
            </flux:button>
        @endif
    </div>

    {{-- Filter Buttons --}}
    <div class="mb-6 flex gap-2">
        <flux:button
            size="sm"
            :variant="$filter === 'all' ? 'primary' : 'ghost'"
            wire:click="setFilter('all')">
            {{ __('All') }}
        </flux:button>
        <flux:button
            size="sm"
            :variant="$filter === 'unread' ? 'primary' : 'ghost'"
            wire:click="setFilter('unread')">
            {{ __('Unread') }}
        </flux:button>
        <flux:button
            size="sm"
            :variant="$filter === 'read' ? 'primary' : 'ghost'"
            wire:click="setFilter('read')">
            {{ __('Read') }}
        </flux:button>
    </div>

    {{-- Pagination Top --}}
    <div class="mb-4 flex justify-end">
        {{ $notifications->links() }}
    </div>

    {{-- Notifications Table --}}
    <div class="flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
                    <table class="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ __('Notification') }}
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 cursor-pointer hover:text-zinc-600 dark:hover:text-zinc-300"
                                    wire:click="setSorting('created_at')">
                                    <div class="flex items-center gap-1">
                                        {{ __('Date') }}
                                        @if ($sortBy === 'created_at')
                                            <x-icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-4 w-4" />
                                        @endif
                                    </div>
                                </th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                            @forelse ($notifications as $notification)
                                <tr class="{{ is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                    <td class="py-4 pr-3 pl-4 text-sm whitespace-nowrap sm:pl-6">
                                        @if (is_null($notification->read_at))
                                            <flux:badge color="blue" size="sm">
                                                {{ __('Unread') }}
                                            </flux:badge>
                                        @else
                                            <flux:badge color="zinc" size="sm">
                                                {{ __('Read') }}
                                            </flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                        <div class="font-medium text-zinc-900 dark:text-white">
                                            {{ data_get($notification->data, 'subject', class_basename($notification->type)) }}
                                        </div>
                                        @if ($message = data_get($notification->data, 'message'))
                                            <div class="mt-1 text-zinc-500 dark:text-zinc-400">
                                                {{ $message }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                        <div class="flex items-center justify-end gap-2">
                                            @if (is_null($notification->read_at))
                                                <flux:button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="check"
                                                    wire:click="markAsRead('{{ $notification->id }}')"
                                                    title="{{ __('Mark as Read') }}" />
                                            @else
                                                <flux:button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon="minus-circle"
                                                    wire:click="markAsUnread('{{ $notification->id }}')"
                                                    title="{{ __('Mark as Unread') }}" />
                                            @endif
                                            <flux:button
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                wire:click="delete('{{ $notification->id }}')"
                                                wire:confirm="{{ __('Are you sure you want to delete this notification?') }}"
                                                title="{{ __('Delete') }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <x-icon name="bell" class="h-12 w-12 text-zinc-400 dark:text-zinc-500" />
                                            <p class="text-sm text-zinc-500 dark:text-zinc-400 italic">
                                                {{ __('No notifications found.') }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination Bottom --}}
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
