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
            <flux:icon.chevron-right x-show="!open" class="size-4!" />
            <flux:icon.chevron-down x-show="open" class="size-4!" />
        </div>
    </flux:navlist.item>

    {{-- Children menu items --}}
    <div x-show="open" x-transition class="ml-6 space-y-1">
        @foreach (data_get($menu, 'children', []) as $child)
            <flux:navlist.item icon="{{ data_get($child, 'icon') }}" :href="data_get($child, 'url')"
                :current="data_get($child, 'active')" wire:navigate class="text-sm">
                {{ data_get($child, 'label') }}
            </flux:navlist.item>
        @endforeach
    </div>
</div>
