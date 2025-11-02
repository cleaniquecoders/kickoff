@props(['menuBuilder'])

@php
    $menu = menu($menuBuilder ?? 'sidebar');
    $menuItems = $menu->menus();
    $isAuthorized = $menu->isAuthorized();
    $authorizationForBlade = $menu->getAuthorizationForBlade();
@endphp

@if ($isAuthorized)
    @if ($authorizationForBlade)
        @can($authorizationForBlade)
            @if ($menuItems->isNotEmpty())
                <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
                    @foreach ($menuItems as $menuItem)
                        @if (data_get($menuItem, 'visible', true))
                            @if (!empty(data_get($menuItem, 'children')))
                                {{-- Parent menu item with children --}}
                                <x-navlist-with-child :menu="$menuItem" />
                            @else
                                {{-- Simple menu item without children --}}
                                @if (data_get($menuItem, 'type', 'link') === 'form')
                                    {{-- Form menu item --}}
                                    <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                                        :current="data_get($menuItem, 'active', false)">
                                        <form method="{{ data_get($menuItem, 'formAttributes.method', 'POST') }}"
                                            action="{{ data_get($menuItem, 'url') }}"
                                            @if (data_get($menuItem, 'formAttributes')) @foreach (data_get($menuItem, 'formAttributes', []) as $attr => $value)
                                                    @if ($attr !== 'method')
                                                        {{ $attr }}="{{ $value }}" @endif
                                            @endforeach
                                @endif>
                                @if (data_get($menuItem, 'formAttributes.method') &&
                                        !in_array(strtoupper(data_get($menuItem, 'formAttributes.method')), ['GET', 'POST']))
                                    @method(data_get($menuItem, 'formAttributes.method'))
                                @endif
                                @csrf
                                <button type="submit"
                                    class="w-full text-left cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded transition-colors duration-150 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                    {{ data_get($menuItem, 'label', 'Menu Item') }}
                                </button>
                                </form>
                                </flux:navlist.item>
                            @else
                                {{-- Link menu item --}}
                                <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                                    :href="data_get($menuItem, 'url')" :current="data_get($menuItem, 'active', false)"
                                    wire:navigate>
                                    {{ data_get($menuItem, 'label', 'Menu Item') }}
                                </flux:navlist.item>
                            @endif
                        @endif
                    @endif
            @endforeach
            </flux:navlist.group>
        @endif
    @endcan
@else
    @if ($menuItems->isNotEmpty())
        <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
            @foreach ($menuItems as $menuItem)
                @if (data_get($menuItem, 'visible', true))
                    @if (!empty(data_get($menuItem, 'children')))
                        {{-- Parent menu item with children --}}
                        <x-navlist-with-child :menu="$menuItem" />
                    @else
                        {{-- Simple menu item without children --}}
                        @if (data_get($menuItem, 'type', 'link') === 'form')
                            {{-- Form menu item --}}
                            <form method="{{ data_get($menuItem, 'formAttributes.method', 'POST') }}"
                                action="{{ data_get($menuItem, 'url') }}"
                                @if (data_get($menuItem, 'formAttributes'))
                                    @foreach (data_get($menuItem, 'formAttributes', []) as $attr => $value)
                                                @if ($attr !== 'method')
                                                    {{ $attr }}="{{ $value }}" @endif
                                    @endforeach
                                @endif>
                                @if (data_get($menuItem, 'formAttributes.method') &&
                                        !in_array(strtoupper(data_get($menuItem, 'formAttributes.method')), ['GET', 'POST']))
                                    @method(data_get($menuItem, 'formAttributes.method'))
                                @endif
                                @csrf
                            <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}" class="cursor-pointer" type="submit"
                                :current="data_get($menuItem, 'active', false)">
                                {{ data_get($menuItem, 'label', 'Menu Item') }}
                            </flux:navlist.item>
                        </form>
                    @else
                        {{-- Link menu item --}}
                        <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                            :href="data_get($menuItem, 'url')" :current="data_get($menuItem, 'active', false)"
                            wire:navigate>
                            {{ data_get($menuItem, 'label', 'Menu Item') }}
                        </flux:navlist.item>
                    @endif
                @endif
            @endif
    @endforeach
    </flux:navlist.group>
@endif
@endif
@endif
