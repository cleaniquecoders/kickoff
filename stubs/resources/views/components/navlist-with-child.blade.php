@props(['menu'])

@php
    $hasActiveChild = collect(data_get($menu, 'children', []))->contains('active', true);
    $menuId = 'menu-' . str()->slug(data_get($menu, 'label'));
@endphp

{{-- Parent menu item --}}
<div x-data="{ open: @js($hasActiveChild) }" class="space-y-1">
    <flux:navlist.item icon="{{ data_get($menu, 'icon') }}" @click="open = !open" class="cursor-pointer">
        <div class="flex items-center justify-between w-full">
            <span>{{ data_get($menu, 'label') }}</span>
            <flux:icon.chevron-right class="size-4! transition-transform duration-200"
                x-bind:class="open && 'rotate-90'" />
        </div>
    </flux:navlist.item>

    {{-- Children menu items --}}
    <div class="navlist-group-panel" x-bind:data-open="open">
        <div class="navlist-group-panel-inner">
            <div class="ml-6 space-y-1">
                @foreach (data_get($menu, 'children', []) as $child)
                    @if (data_get($child, 'target') === '_blank')
                        <flux:navlist.item icon="{{ data_get($child, 'icon') }}" :href="data_get($child, 'url')"
                            :current="data_get($child, 'active')" class="text-sm"
                            target="_blank" rel="noopener noreferrer">
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ data_get($child, 'label') }}</span>
                                <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
                            </span>
                        </flux:navlist.item>
                    @else
                        <flux:navlist.item icon="{{ data_get($child, 'icon') }}" :href="data_get($child, 'url')"
                            :current="data_get($child, 'active')" wire:navigate class="text-sm">
                            {{ data_get($child, 'label') }}
                        </flux:navlist.item>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
