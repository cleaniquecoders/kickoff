@props(['menu'])

@php
    $hasActiveChild = collect(data_get($menu, 'children', []))->contains('active', true);
@endphp

{{-- Nested sub-group — same right-chevron header style as the top-level group,
     with the subtle vertical guide line. On SPA navigation Alpine preserves
     `open`, so re-sync it to the freshly rendered server state (data-group-expanded)
     on livewire:navigated — otherwise a sub-group stays open after you navigate
     away from its active child. --}}
<div x-data="{ open: @js($hasActiveChild) }"
    data-group-expanded="{{ $hasActiveChild ? '1' : '0' }}"
    x-on:livewire:navigated.window="open = ($el.dataset.groupExpanded === '1')"
    class="space-y-1">
    <flux:navlist.item icon="{{ data_get($menu, 'icon') }}" @click="open = !open" class="cursor-pointer">
        <div class="flex items-center justify-between w-full">
            <span class="min-w-0 truncate">{{ data_get($menu, 'label') }}</span>
            <flux:icon.chevron-right class="size-4! shrink-0 transition-transform duration-200"
                x-bind:class="open && 'rotate-90'" />
        </div>
    </flux:navlist.item>

    {{-- Height animates via grid-template-rows applied as INLINE styles (not Tailwind
         arbitrary classes) so it collapses even if those utilities are absent from the
         compiled stylesheet. The vertical guide line matches the top-level group. --}}
    <div class="grid" style="grid-template-rows: {{ $hasActiveChild ? '1fr' : '0fr' }};"
        x-bind:style="{ gridTemplateRows: open ? '1fr' : '0fr', transition: 'grid-template-rows 200ms ease-out' }">
        <div style="overflow: hidden;">
            <div class="relative space-y-[2px] ps-7 pt-1">
                <div class="absolute inset-y-[3px] start-0 ms-4 w-px bg-zinc-200 dark:bg-white/30"></div>

                @foreach (data_get($menu, 'children', []) as $child)
                    @if (data_get($child, 'target') === '_blank')
                        <flux:navlist.item icon="{{ data_get($child, 'icon') }}" :href="data_get($child, 'url')"
                            :current="data_get($child, 'active')" class="text-sm"
                            target="_blank" rel="noopener noreferrer" title="{{ data_get($child, 'label') }}">
                            <span class="flex w-full items-center justify-between gap-2 min-w-0">
                                <span class="min-w-0 truncate">{{ data_get($child, 'label') }}</span>
                                <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
                            </span>
                        </flux:navlist.item>
                    @else
                        <flux:navlist.item icon="{{ data_get($child, 'icon') }}" :href="data_get($child, 'url')"
                            :current="data_get($child, 'active')" wire:navigate class="text-sm"
                            title="{{ data_get($child, 'label') }}">
                            <span class="block truncate">{{ data_get($child, 'label') }}</span>
                        </flux:navlist.item>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
