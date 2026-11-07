{{-- The rows of one area's panel: a parent with children is a disclosure, anything
     else is a link. `touch` makes every row 44px for the mobile drawer. --}}
@props(['items', 'touch' => false])

@php
    $row = $touch ? 'h-11!' : '';
@endphp

@foreach ($items as $item)
    @if (! empty($item['children']))
        <x-navlist-with-child :menu="$item" />
    @elseif (($item['target'] ?? '_self') === '_blank')
        <flux:navlist.item :icon="$item['icon']" :href="$item['url']" :current="$item['active']"
            target="_blank" rel="noopener noreferrer" :class="$row">
            <span class="flex w-full min-w-0 items-center justify-between gap-2">
                <span class="min-w-0 truncate">{{ $item['label'] }}</span>
                <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
                <span class="sr-only">{{ __('(opens in a new tab)') }}</span>
            </span>
        </flux:navlist.item>
    @else
        <flux:navlist.item :icon="$item['icon']" :href="$item['url']" :current="$item['active']" wire:navigate :class="$row">
            <span class="block truncate" title="{{ $item['label'] }}">{{ $item['label'] }}</span>
        </flux:navlist.item>
    @endif
@endforeach
