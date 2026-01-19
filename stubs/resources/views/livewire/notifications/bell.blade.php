<div>
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="ghost" icon="bell" class="relative">
            @if ($this->unreadCount > 0)
                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white">
                    {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
                </span>
            @endif
        </flux:button>

        <flux:menu class="w-80">
            <flux:menu.heading class="flex items-center justify-between">
                <span>{{ __('Notifications') }}</span>
                @if ($this->unreadCount > 0)
                    <flux:badge color="amber" size="sm">
                        {{ $this->unreadCount }} {{ __('unread') }}
                    </flux:badge>
                @endif
            </flux:menu.heading>

            <flux:menu.separator />

            @forelse ($this->recentNotifications as $notification)
                <flux:menu.item
                    wire:click="markAsRead('{{ $notification->id }}')"
                    class="flex flex-col items-start gap-1 !py-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span class="font-medium text-zinc-900 dark:text-white">
                            {{ data_get($notification->data, 'title', class_basename($notification->type)) }}
                        </span>
                    </div>
                    @if ($message = data_get($notification->data, 'message'))
                        <span class="ml-4 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2">
                            {{ $message }}
                        </span>
                    @endif
                    <span class="ml-4 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </flux:menu.item>
            @empty
                <div class="py-6 text-center">
                    <x-icon name="bell" class="mx-auto h-8 w-8 text-zinc-400 dark:text-zinc-500" />
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No unread notifications') }}
                    </p>
                </div>
            @endforelse

            <flux:menu.separator />

            <flux:menu.item :href="route('notifications.index')" wire:navigate icon="chevron-right">
                {{ __('View All Notifications') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>
