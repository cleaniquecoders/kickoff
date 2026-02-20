@props(['width' => 'w-[220px]'])

<flux:menu {{ $attributes->merge(['class' => $width]) }}>
    {{-- User Info --}}
    <div class="p-2 text-sm">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                {{ auth()->user()->initials() }}
            </span>
            <div class="flex-1 min-w-0">
                <div class="truncate font-semibold">{{ auth()->user()->name }}</div>
                <div class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</div>
            </div>
        </div>
    </div>

    <flux:menu.separator />

    {{-- Settings Links --}}
    <flux:menu.item :href="route('settings.profile.edit')" icon="user-circle" wire:navigate>
        {{ __('Profile') }}
    </flux:menu.item>
    <flux:menu.item :href="route('settings.user-password.edit')" icon="lock-closed" wire:navigate>
        {{ __('Password') }}
    </flux:menu.item>
    <flux:menu.item :href="route('settings.appearance.edit')" icon="sun" wire:navigate>
        {{ __('Appearance') }}
    </flux:menu.item>

    <flux:menu.separator />

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('Log Out') }}
        </flux:menu.item>
    </form>
</flux:menu>
