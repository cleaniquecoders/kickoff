{{-- Rail + panel sidebar, used once a user can see two or more sections (NavRail).
     Below 1024px there is no rail: Flux's stashable sidebar becomes the drawer. --}}
@props(['nav'])

@php
    // Plain cookie (bootstrap/app.php encryptCookies exception), so the pin renders
    // server-side under wire:navigate without flicker.
    $panelPref = in_array(request()->cookie('nav_panel'), ['pinned', 'unpinned'], true)
        ? request()->cookie('nav_panel')
        : 'auto';

    $current = $nav['current'];

    $areaLink = 'relative flex w-14 flex-col items-center gap-0.5 rounded-lg border border-transparent pb-1 pt-1.5 text-[11px] font-medium leading-tight text-zinc-600 hover:bg-zinc-800/5 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent aria-[current]:border-zinc-200 aria-[current]:bg-white aria-[current]:text-accent-content dark:text-zinc-400 dark:hover:bg-white/[7%] dark:hover:text-white dark:aria-[current]:border-white/10 dark:aria-[current]:bg-white/[8%] dark:aria-[current]:text-accent-content';
    $railButton = 'relative flex size-11 cursor-pointer items-center justify-center rounded-lg text-zinc-600 hover:bg-zinc-800/5 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent aria-[current]:text-accent-content dark:text-zinc-400 dark:hover:bg-white/[7%] dark:hover:text-white';
    $drawerRow = 'flex h-11 w-full items-center gap-3 rounded-lg px-3 text-start text-sm font-medium text-zinc-700 hover:bg-zinc-800/5 focus-visible:outline-2 focus-visible:outline-accent aria-[current]:text-accent-content dark:text-zinc-300 dark:hover:bg-white/[7%]';
@endphp

<flux:sidebar stashable x-data="navShell" data-nav-panel="{{ $panelPref }}" data-nav-current="{{ $current['key'] ?? '' }}"
    x-bind:data-nav-panel="pref" x-on:mouseleave="leave()" x-on:keydown.escape="escape()"
    class="p-0! gap-0! w-80 max-w-[85vw] border-e border-zinc-200 bg-zinc-50 max-lg:overflow-y-auto dark:border-white/10 dark:bg-zinc-900 lg:sticky lg:top-0 lg:z-30 lg:h-dvh lg:w-auto lg:max-w-none lg:flex-row lg:self-start lg:border-e-0 lg:bg-transparent">

    {{-- Desktop rail --}}
    <nav class="hidden h-dvh w-16 shrink-0 flex-col items-center gap-1 overflow-y-auto border-e border-zinc-200 bg-zinc-50 py-3 lg:flex dark:border-white/10 dark:bg-zinc-950"
        aria-label="{{ __('Main') }}">
        <a href="{{ route('dashboard') }}" wire:navigate aria-label="{{ __('Dashboard') }}" title="{{ __('Dashboard') }}"
            @if (request()->routeIs('dashboard')) aria-current="page" @endif
            class="mb-1 flex size-11 items-center justify-center rounded-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
            <x-app-logo-icon class="size-8 text-accent-content" />
        </a>

        <div class="mt-2 flex flex-col items-center gap-1">
            @foreach ($nav['areas'] as $area)
                <a href="{{ $area['landing'] }}" data-nav-area="{{ $area['key'] }}"
                    data-nav-urls="{{ json_encode(array_column(\App\Actions\Builder\Menu\NavRail::leaves($area['items']), 'url')) }}"
                    x-on:click="go($event)" x-on:mouseenter="intent('{{ $area['key'] }}')" x-on:mouseleave="cancelIntent()"
                    x-on:focus="cancelIntent()" x-on:keydown.arrow-right.prevent="openFromKey('{{ $area['key'] }}')"
                    title="{{ $area['heading'] }}" aria-label="{{ $area['heading'] }}"
                    @if ($area['current']) aria-current="true" @endif
                    class="{{ $areaLink }}">
                    <flux:icon :icon="$area['icon']" class="size-5" />
                    <span class="max-w-full truncate px-0.5" aria-hidden="true">{{ $area['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-auto flex flex-col items-center gap-1 pt-3">
            <button type="button" x-on:click="$dispatch('nav-palette-open')" class="{{ $railButton }}"
                aria-label="{{ __('Search pages') }}" title="{{ __('Search pages (Ctrl K)') }}" aria-keyshortcuts="Control+K Meta+K">
                <flux:icon.search class="size-5" />
            </button>

            @foreach ($nav['globals'] as $link)
                <a href="{{ $link['url'] }}" wire:navigate class="{{ $railButton }}" aria-label="{{ $link['label'] }}"
                    title="{{ $link['label'] }}" @if ($link['active']) aria-current="page" @endif>
                    <flux:icon :icon="$link['icon']" class="size-5" />
                </a>
            @endforeach

            @if ($nav['help'] !== [])
                <flux:dropdown position="right" align="end">
                    <button type="button" class="{{ $railButton }}" aria-label="{{ __('Help') }}" title="{{ __('Help') }}">
                        <flux:icon.circle-question-mark class="size-5" />
                    </button>
                    <flux:menu>
                        @foreach ($nav['help'] as $link)
                            @if (($link['target'] ?? '_self') === '_blank')
                                <flux:menu.item :icon="$link['icon']" :href="$link['url']" target="_blank" rel="noopener noreferrer">{{ $link['label'] }}</flux:menu.item>
                            @else
                                <flux:menu.item :icon="$link['icon']" :href="$link['url']" wire:navigate>{{ $link['label'] }}</flux:menu.item>
                            @endif
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
            @endif

            <flux:dropdown position="right" align="end">
                <flux:profile :initials="auth()->user()->initials()" :chevron="false" :aria-label="__('Account: :name', ['name' => auth()->user()->name])" />

                <x-user-menu />
            </flux:dropdown>
        </div>
    </nav>

    {{-- Docked panel: the current area. CSS decides docked or not from data-nav-panel. --}}
    @if ($current && $current['hasPanel'])
        <aside data-nav-docked class="nav-docked hidden h-dvh w-58 shrink-0 border-e border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900"
            aria-label="{{ $current['heading'] }}">
            <x-nav.panel :area="$current" pin />
        </aside>
    @endif

    {{-- Overlay panels: open on hover intent or → from the rail, and never reflow the page. --}}
    <div class="hidden lg:block">
        @foreach ($nav['areas'] as $area)
            @if ($area['hasPanel'])
                <aside data-nav-overlay="{{ $area['key'] }}" x-show="open === '{{ $area['key'] }}'" x-cloak
                    x-transition.opacity.duration.150ms x-on:mouseenter="stay()"
                    class="absolute inset-y-0 start-16 z-40 flex h-dvh w-58 border-e border-zinc-200 bg-white shadow-xl dark:border-white/10 dark:bg-zinc-900"
                    aria-label="{{ $area['heading'] }}">
                    <x-nav.panel :area="$area" pin />
                </aside>
            @endif
        @endforeach
    </div>

    {{-- Mobile drawer --}}
    <div x-data="navDrawer" x-trap.noscroll="openMobile" x-on:keydown.escape.stop="close()" x-bind:inert="! openMobile"
        class="flex min-h-dvh flex-col gap-3 p-3 lg:hidden" role="dialog" x-bind:aria-modal="openMobile ? 'true' : null" aria-label="{{ __('Navigation') }}">
        <div class="flex items-center justify-between gap-2">
            <a href="{{ route('dashboard') }}" wire:navigate class="rounded-lg focus-visible:outline-2 focus-visible:outline-accent">
                <x-app-logo />
            </a>
            <flux:sidebar.toggle icon="x-mark" class="size-11!" />
        </div>

        <nav aria-label="{{ __('Main') }}" class="flex flex-col gap-1">
            @foreach ($nav['globals'] as $link)
                <a href="{{ $link['url'] }}" wire:navigate class="{{ $drawerRow }}" @if ($link['active']) aria-current="page" @endif>
                    <flux:icon :icon="$link['icon']" class="size-5 shrink-0" />
                    <span class="flex-1 truncate">{{ $link['label'] }}</span>
                </a>
            @endforeach

            @foreach ($nav['areas'] as $area)
                @if ($area['hasPanel'])
                    <div x-data="{ expanded: @js($area['current']) }">
                        <button type="button" x-on:click="expanded = ! expanded" x-bind:aria-expanded="expanded ? 'true' : 'false'"
                            @if ($area['current']) aria-current="true" @endif class="{{ $drawerRow }} cursor-pointer">
                            <flux:icon :icon="$area['icon']" class="size-5 shrink-0" />
                            <span class="flex-1 truncate">{{ $area['heading'] }}</span>
                            <flux:icon.chevron-right class="size-4 shrink-0 motion-safe:transition-transform" x-bind:class="expanded && 'rotate-90'" />
                        </button>
                        <div x-show="expanded" @if (! $area['current']) x-cloak @endif class="ps-3">
                            <x-nav.panel :area="$area" touch />
                        </div>
                    </div>
                @else
                    <a href="{{ $area['landing'] }}" wire:navigate class="{{ $drawerRow }}" @if ($area['current']) aria-current="page" @endif>
                        <flux:icon :icon="$area['icon']" class="size-5 shrink-0" />
                        <span class="flex-1 truncate">{{ $area['heading'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        @if ($nav['help'] !== [])
            <div class="mt-auto border-t border-zinc-200 pt-3 dark:border-white/10">
                <flux:navlist variant="outline" :aria-label="__('Help')">
                    <x-nav.panel-items :items="$nav['help']" touch />
                </flux:navlist>
            </div>
        @endif
    </div>
</flux:sidebar>
