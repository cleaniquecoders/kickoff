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
            <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
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
