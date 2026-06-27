@props([
    'for' => null,      // explicit route name to resolve against the menu tree
    'params' => [],     // route params for the explicit route
    'leaf' => null,     // current-page label to append (rendered without a link)
    'items' => null,    // manual trail: array of ['label' => ..., 'url' => ...] (Dashboard auto-prepended)
    'trail' => null,    // a pre-built App\Actions\Builder\Breadcrumb instance
])

@php
    /**
     * Breadcrumbs derive from the same menu tree that renders the sidebar, so the
     * trail always matches navigation. Common usage:
     *   <x-breadcrumbs />                                        {{-- auto: page is a menu leaf --}}
     *   <x-breadcrumbs for="admin.roles.index" leaf="Role Details" /> {{-- detail page --}}
     *   <x-breadcrumbs :items="[['label' => __('Administration')]]" /> {{-- hub / non-menu page --}}
     */
    $builder = $trail instanceof \App\Actions\Builder\Breadcrumb
        ? $trail
        : (is_array($items)
            ? \App\Actions\Builder\Breadcrumb::manual($items)
            : ($for
                ? \App\Actions\Builder\Breadcrumb::for($for, $params)
                : \App\Actions\Builder\Breadcrumb::current()));

    if (! is_null($leaf)) {
        $builder->push($leaf);
    }

    $crumbs = $builder->items();
    $lastIndex = count($crumbs) - 1;
@endphp

@if (! empty($crumbs))
    <flux:breadcrumbs {{ $attributes }}>
        @foreach ($crumbs as $index => $crumb)
            @if (! empty($crumb['url']) && $crumb['url'] !== '#' && $index !== $lastIndex)
                <flux:breadcrumbs.item :href="$crumb['url']" wire:navigate>{{ $crumb['label'] }}</flux:breadcrumbs.item>
            @else
                <flux:breadcrumbs.item>{{ $crumb['label'] }}</flux:breadcrumbs.item>
            @endif
        @endforeach
    </flux:breadcrumbs>
@endif
