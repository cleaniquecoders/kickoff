@props([
    'expandable' => false,
    'expanded' => true,
    'heading' => null,
    'icon' => null,
])

<?php if ($expandable && $heading): ?>

<ui-disclosure
    {{ $attributes->class('group/disclosure') }}
    @if ($expanded === true) open @endif
    data-flux-navlist-group
>
    {{-- Header reads [icon] Label … [chevron-right] — matching the leaf nav items
         and the g8stack sidebar. The chevron sits on the trailing edge and rotates
         on open via the disclosure's data-open state. --}}
    <button
        type="button"
        class="group/disclosure-button mb-[2px] flex h-10 w-full cursor-pointer items-center gap-3 rounded-lg px-3 text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800 lg:h-8 dark:text-white/80 dark:hover:bg-white/[7%] dark:hover:text-white"
    >
        @if ($icon)
            <flux:icon icon="{{ $icon }}" class="size-4 shrink-0" />
        @endif

        <span class="flex-1 text-start text-sm font-medium leading-none truncate">{{ $heading }}</span>

        <flux:icon.chevron-right class="size-4 shrink-0 transition-transform duration-200 group-data-open/disclosure-button:rotate-90" />
    </button>

    <div class="navlist-group-panel" @if ($expanded === true) data-open @endif>
        <div class="navlist-group-panel-inner">
            <div class="relative space-y-[2px] ps-7">
                <div class="absolute inset-y-[3px] start-0 ms-4 w-px bg-zinc-200 dark:bg-white/30"></div>

                {{ $slot }}
            </div>
        </div>
    </div>
</ui-disclosure>

<?php elseif ($heading): ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    <div class="px-1 py-2">
        <div class="text-xs leading-none text-zinc-400">{{ $heading }}</div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>

<?php else: ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    {{ $slot }}
</div>

<?php endif; ?>
