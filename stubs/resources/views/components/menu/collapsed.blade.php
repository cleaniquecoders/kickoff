{{-- Icon-rail variant (desktop collapsed sidebar) — included by components/menu.blade.php.
     Expects: $menu, $menuItems, $heading, $headingIcon, $hasActiveItem --}}
@if (filled($heading))
    {{-- Group → flyout submenu next to the rail --}}
    <flux:dropdown position="right" align="start">
        <flux:button variant="ghost" size="sm" icon="{{ $headingIcon ?? 'circle' }}"
            class="w-full cursor-pointer {{ $hasActiveItem ? 'text-accent-content bg-zinc-800/5 dark:bg-white/10' : '' }}"
            data-tippy-content="{{ $heading }}" />

        <flux:menu class="min-w-48">
            <div class="px-2 py-1.5 text-xs font-medium text-zinc-400">{{ $heading }}</div>
            <flux:menu.separator />

            @foreach ($menuItems as $menuItem)
                @continue(! data_get($menuItem, 'visible', true))

                @if (! empty(data_get($menuItem, 'children')))
                    {{-- Children flattened under their parent label --}}
                    <flux:menu.separator />
                    <div class="px-2 py-1.5 text-xs font-medium text-zinc-400">
                        {{ data_get($menuItem, 'label') }}
                    </div>
                    @foreach (data_get($menuItem, 'children', []) as $child)
                        @if (data_get($child, 'target') === '_blank')
                            <flux:menu.item icon="{{ data_get($child, 'icon', 'circle') }}"
                                :href="data_get($child, 'url')" target="_blank" rel="noopener noreferrer">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
                                    <span>{{ data_get($child, 'label') }}</span>
                                </span>
                            </flux:menu.item>
                        @else
                            <flux:menu.item icon="{{ data_get($child, 'icon', 'circle') }}"
                                :href="data_get($child, 'url')" wire:navigate>
                                {{ data_get($child, 'label') }}
                            </flux:menu.item>
                        @endif
                    @endforeach
                @elseif (data_get($menuItem, 'type', 'link') === 'form')
                    @php $formMethod = strtoupper(data_get($menuItem, 'formAttributes.method', 'POST')); @endphp
                    <form method="{{ $formMethod === 'GET' ? 'GET' : 'POST' }}" action="{{ data_get($menuItem, 'url') }}"
                        class="w-full"
                        @foreach (data_get($menuItem, 'formAttributes', []) as $attr => $value)
                            @if ($attr !== 'method') {{ $attr }}="{{ $value }}" @endif
                        @endforeach>
                        @if (! in_array($formMethod, ['GET', 'POST']))
                            @method($formMethod)
                        @endif
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                            class="w-full">
                            {{ data_get($menuItem, 'label', 'Menu Item') }}
                        </flux:menu.item>
                    </form>
                @else
                    @if (data_get($menuItem, 'target') === '_blank')
                        <flux:menu.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                            :href="data_get($menuItem, 'url')" target="_blank" rel="noopener noreferrer">
                            <span class="inline-flex items-center gap-1.5">
                                <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
                                <span>{{ data_get($menuItem, 'label', 'Menu Item') }}</span>
                            </span>
                        </flux:menu.item>
                    @else
                        <flux:menu.item icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                            :href="data_get($menuItem, 'url')" wire:navigate>
                            {{ data_get($menuItem, 'label', 'Menu Item') }}
                        </flux:menu.item>
                    @endif
                @endif
            @endforeach
        </flux:menu>
    </flux:dropdown>
@else
    {{-- Heading-less builder → icon-only items with tooltips --}}
    @foreach ($menuItems as $menuItem)
        @continue(! data_get($menuItem, 'visible', true))

        @if (! empty(data_get($menuItem, 'children')))
            <flux:dropdown position="right" align="start">
                <flux:button variant="ghost" size="sm" icon="{{ data_get($menuItem, 'icon', 'circle') }}"
                    class="w-full cursor-pointer" data-tippy-content="{{ data_get($menuItem, 'label') }}" />

                <flux:menu class="min-w-48">
                    @foreach (data_get($menuItem, 'children', []) as $child)
                        @if (data_get($child, 'target') === '_blank')
                            <flux:menu.item icon="{{ data_get($child, 'icon', 'circle') }}"
                                :href="data_get($child, 'url')" target="_blank" rel="noopener noreferrer">
                                <span class="inline-flex items-center gap-1.5">
                                    <flux:icon.arrow-top-right-on-square class="size-3.5 shrink-0 opacity-60" />
                                    <span>{{ data_get($child, 'label') }}</span>
                                </span>
                            </flux:menu.item>
                        @else
                            <flux:menu.item icon="{{ data_get($child, 'icon', 'circle') }}"
                                :href="data_get($child, 'url')" wire:navigate>
                                {{ data_get($child, 'label') }}
                            </flux:menu.item>
                        @endif
                    @endforeach
                </flux:menu>
            </flux:dropdown>
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
                    data-tippy-content="{{ data_get($menuItem, 'label') }}" />
            </form>
        @elseif (data_get($menuItem, 'target') === '_blank')
            <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}" :href="data_get($menuItem, 'url')"
                :current="data_get($menuItem, 'active', false)"
                data-tippy-content="{{ data_get($menuItem, 'label') }}" target="_blank" rel="noopener noreferrer" />
        @else
            <flux:navlist.item icon="{{ data_get($menuItem, 'icon', 'circle') }}" :href="data_get($menuItem, 'url')"
                :current="data_get($menuItem, 'active', false)" wire:navigate
                data-tippy-content="{{ data_get($menuItem, 'label') }}" />
        @endif
    @endforeach
@endif
