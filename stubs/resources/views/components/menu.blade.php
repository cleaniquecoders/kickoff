@props(['menuBuilder'])

@php
    $menu = menu($menuBuilder);
@endphp

@if ($menu->isAuthorized())
    @if ($menu->getAuthorizationForBlade())
        @can($menu->getAuthorizationForBlade())
            <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
                @foreach ($menu->menus() as $menuItem)
                    @isset($menuItem['children'])
                        @if (!empty($menuItem['children']))
                            {{-- Parent menu item with children --}}
                            <x-navlist-with-child :menu="$menuItem" />
                        @else
                            {{-- Simple menu item without children --}}
                            <flux:navlist.item icon="{{ data_get($menuItem, 'icon') }}" :href="data_get($menuItem, 'url')"
                                :current="data_get($menuItem, 'active')" wire:navigate>{{ data_get($menuItem, 'label') }}
                            </flux:navlist.item>
                        @endif
                    @else
                        {{-- Simple menu item without children --}}
                        <flux:navlist.item icon="{{ data_get($menuItem, 'icon') }}" :href="data_get($menuItem, 'url')"
                            :current="data_get($menuItem, 'active')" wire:navigate>{{ data_get($menuItem, 'label') }}
                        </flux:navlist.item>
                    @endisset
                @endforeach
            </flux:navlist.group>
        @endcan
    @else
        <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
            @foreach ($menu->menus() as $menuItem)
                @isset($menuItem['children'])
                    @if (!empty($menuItem['children']))
                        {{-- Parent menu item with children --}}
                        <x-navlist-with-child :menu="$menuItem" />
                    @else
                        {{-- Simple menu item without children --}}
                        <flux:navlist.item icon="{{ data_get($menuItem, 'icon') }}" :href="data_get($menuItem, 'url')"
                            :current="data_get($menuItem, 'active')" wire:navigate>{{ data_get($menuItem, 'label') }}
                        </flux:navlist.item>
                    @endif
                @else
                    {{-- Simple menu item without children --}}
                    <flux:navlist.item icon="{{ data_get($menuItem, 'icon') }}" :href="data_get($menuItem, 'url')"
                        :current="data_get($menuItem, 'active')" wire:navigate>{{ data_get($menuItem, 'label') }}
                    </flux:navlist.item>
                @endisset
            @endforeach
        </flux:navlist.group>
    @endif
@endif
