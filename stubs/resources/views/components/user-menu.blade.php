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

    {{-- Theme Toggle --}}
    <div class="flex items-center justify-between px-2 py-1.5">
        <div class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300">
            <svg x-show="!darkMode" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="darkMode" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <span>{{ __('Theme') }}</span>
        </div>
        <button
            @click="window.toggleDarkMode()"
            class="relative flex h-6 w-11 items-center rounded-full bg-zinc-200 p-0.5 transition-colors dark:bg-zinc-600"
        >
            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-sm transition-transform dark:translate-x-5"></span>
        </button>
    </div>

    <flux:menu.separator />

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('Log Out') }}
        </flux:menu.item>
    </form>
</flux:menu>
