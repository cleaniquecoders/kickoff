{{--
    Host layout for full-page Livewire admin screens shipped by vendor packages
    (config-backup, config-sso, config-webhook) whose own views we don't control
    and therefore can't add <x-breadcrumbs> to directly.

    It wraps the standard app layout and injects the menu-derived breadcrumb above
    the page. Because the breadcrumb lives in the LAYOUT (rendered once on the
    initial GET, never re-rendered on a Livewire update), Breadcrumb::current()
    sees the real page URL and the trail stays correct across wire interactions.

    Point a package's host-layout config at this view, e.g.
        'layout' => 'components.layouts.app-breadcrumbs',
--}}
<x-layouts.app :title="$title ?? null">
    <x-breadcrumbs class="mb-6" />
    {{ $slot }}
</x-layouts.app>
