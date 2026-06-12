{{-- Expanded sidebar variant — included by components/menu.blade.php.
     Expects: $menu, $menuItems, $heading, $hasActiveItem --}}
<flux:navlist.group :heading="$heading" :expandable="filled($heading)" class="grid">
    @foreach ($menuItems as $menuItem)
        @continue(! data_get($menuItem, 'visible', true))

        @if (! empty(data_get($menuItem, 'children')))
            {{-- Parent menu item with children --}}
            <x-navlist-with-child :menu="$menuItem" />
        @elseif (data_get($menuItem, 'type', 'link') === 'form')
            {{-- Form menu item --}}
            @php $formMethod = strtoupper(data_get($menuItem, 'formAttributes.method', 'POST')); @endphp
            <form method="{{ $formMethod === 'GET' ? 'GET' : 'POST' }}" action="{{ data_get($menuItem, 'url') }}"
                @foreach (data_get($menuItem, 'formAttributes', []) as $attr => $value)
                    @if ($attr !== 'method') {{ $attr }}="{{ $value }}" @endif
                @endforeach>
                @if (! in_array($formMethod, ['GET', 'POST']))
                    @method($formMethod)
                @endif
                @csrf
                <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}" as="button" type="submit"
                    class="w-full cursor-pointer" :current="data_get($menuItem, 'active', false)">
                    {{ data_get($menuItem, 'label', 'Menu Item') }}
                </flux:navlist.item>
            </form>
        @else
            {{-- Link menu item --}}
            <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}" :href="data_get($menuItem, 'url')"
                :current="data_get($menuItem, 'active', false)" wire:navigate>
                {{ data_get($menuItem, 'label', 'Menu Item') }}
            </flux:navlist.item>
        @endif
    @endforeach
</flux:navlist.group>
