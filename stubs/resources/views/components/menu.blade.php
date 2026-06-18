@props(['menuBuilder'])

@php
    $menu = menu($menuBuilder ?? 'sidebar');
    $menuItems = $menu->menus();

    // isAuthorized() already evaluates string authorizations via Gate::allows().
    $canView = $menu->isAuthorized() && $menuItems->isNotEmpty();

    $heading = $menu->getHeadingLabel();
    $headingIcon = $menu->getHeadingIcon();
    $hasActiveItem = $menuItems->contains(
        fn ($menuItem) => data_get($menuItem, 'active', false)
            || collect(data_get($menuItem, 'children', []))->contains(fn ($child) => data_get($child, 'active', false))
    );
@endphp

@if ($canView)
    {{-- Full sidebar --}}
    <div class="sidebar-expanded-only">
        @include('components.menu.expanded')
    </div>

    {{-- Icon rail (desktop collapsed mode) --}}
    <div class="sidebar-collapsed-only">
        @include('components.menu.collapsed')
    </div>
@endif
