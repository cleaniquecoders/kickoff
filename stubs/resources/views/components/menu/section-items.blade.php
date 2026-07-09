{{-- Renders a section's menu items WITHOUT a group heading — the section
     switcher dropdown already names the active section, so repeating it as a
     heading would be redundant. Item markup mirrors components/menu/expanded.blade
     (children → nested sub-group, form, link). Expects: $menuItems (collection). --}}
@foreach ($menuItems as $menuItem)
    @continue(! data_get($menuItem, 'visible', true))

    @if (! empty(data_get($menuItem, 'children')))
        <x-navlist-with-child :menu="$menuItem" />
    @elseif (data_get($menuItem, 'type', 'link') === 'form')
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
                class="w-full cursor-pointer" :current="data_get($menuItem, 'active', false)"
                title="{{ data_get($menuItem, 'label', 'Menu Item') }}">
                <span class="block truncate">{{ data_get($menuItem, 'label', 'Menu Item') }}</span>
            </flux:navlist.item>
        </form>
    @elseif (data_get($menuItem, 'target') === '_blank')
        <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
            :href="data_get($menuItem, 'url')" :current="data_get($menuItem, 'active', false)"
            target="_blank" rel="noopener noreferrer" title="{{ data_get($menuItem, 'label', 'Menu Item') }}">
            <span class="flex w-full items-center justify-between gap-2 min-w-0">
                <span class="min-w-0 truncate">{{ data_get($menuItem, 'label', 'Menu Item') }}</span>
                <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
            </span>
        </flux:navlist.item>
    @else
        <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
            :href="data_get($menuItem, 'url')" :current="data_get($menuItem, 'active', false)" wire:navigate
            title="{{ data_get($menuItem, 'label', 'Menu Item') }}">
            <span class="block truncate">{{ data_get($menuItem, 'label', 'Menu Item') }}</span>
        </flux:navlist.item>
    @endif
@endforeach
