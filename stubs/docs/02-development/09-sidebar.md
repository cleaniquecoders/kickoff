# Sidebar System

This document explains the sidebar system implementation, including menu builders, authorization, and how to create and manage sidebar menus.

## Overview

The sidebar system provides a flexible, modular approach to building navigation menus with proper authorization checks. It uses a builder pattern to create menu items with consistent structure and authorization.

## Architecture

### Layout

The active sidebar layout is located at:

```
resources/views/components/layouts/app/sidebar.blade.php
```

This layout is wrapped by `resources/views/components/layouts/app.blade.php` which simply renders:

```blade
<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
```

The sidebar layout includes:
- Flux sidebar component with sticky/stashable configuration
- Global items (`<x-menu>`), the section switcher (`<x-section-switcher>`), and footer items
- Desktop and mobile user menu dropdowns
- Toast notification system
- Session message to toast conversion
- `@auth` / `@else` guard for unauthenticated users

### Sidebar composition — `config/menu.php`

`config/menu.php` is the **single source of truth** for how the sidebar is
composed. Every value is a menu-builder key (resolved by `app/Actions/Builder/Menu.php`):

```php
return [
    'globals'  => ['sidebar'],                             // pinned on top (no heading)
    'sections' => ['administration', 'media-management'],  // shown one-at-a-time via the switcher
    'footer'   => ['sidebar-footer'],                      // pinned to the bottom
];
```

The layout renders globals, then the section switcher, then the footer:

```blade
<flux:navlist variant="outline">
    @foreach (config('menu.globals') as $builder)
        <x-menu :menu-builder="$builder" />
    @endforeach
</flux:navlist>

<x-section-switcher />

<flux:spacer />

<flux:navlist variant="outline">
    @foreach (config('menu.footer') as $builder)
        <x-menu :menu-builder="$builder" />
    @endforeach
</flux:navlist>
```

The breadcrumb builder (`app/Actions/Builder/Breadcrumb.php`) reads this **same**
list, so navigation and breadcrumbs can never drift.

### Section switcher

`sections` are **not** stacked as groups. A single dropdown
(`resources/views/components/section-switcher.blade.php`) names the active
section; the sidebar shows only that section's items. Picking a section
navigates (`wire:navigate`) to its landing page.

`App\Actions\Builder\Menu\SectionResolver` builds the sections. For each key in
`config('menu.sections')` it:

- drops the section if the user can't see it (unauthorized or empty);
- takes its **label / icon / landing** from the builder heading
  (`getHeadingLabel` / `getHeadingIcon` / `getHeadingUrl`) — no duplicated
  metadata — falling back to the first item's URL for the landing;
- marks the **active** section by longest URL-prefix match, so a sub-page like
  `/admin/roles/42/edit` keeps Administration selected even though no menu item
  matches that exact URL.

In the collapsed rail each section becomes a flyout icon (shared
`components/menu/collapsed.blade.php`). When no section is authorized the
switcher renders nothing.

### Menu builder classes

Located in `app/Actions/Builder/Menu/`:

| Class | Builder key | Role | Heading |
|-------|-------------|------|---------|
| `Base.php` | — | Abstract base class | — |
| `Sidebar.php` | `sidebar` | Global (pinned top) | *(none)* — Dashboard, Notifications |
| `Administration.php` | `administration` | Section | Administration → `admin.index` |
| `MediaManagement.php` | `media-management` | Section | Media |
| `SidebarFooter.php` | `sidebar-footer` | Footer (pinned bottom) | Resources |
| `SectionResolver.php` | — | Resolves sections for the switcher | — |

### Menu router

`app/Actions/Builder/Menu.php` routes builder keys to classes:

```php
$class = match ($builder) {
    'sidebar' => Sidebar::class,
    'administration' => Administration::class,
    'media-management' => MediaManagement::class,
    'sidebar-footer' => SidebarFooter::class,
    default => Sidebar::class,
};
```

## Base Menu Builder

The `Base` class provides core functionality for all menu builders:

```php
abstract class Base implements AuthorizedMenuBuilder, Builder, HeadingMenuBuilder, Menu
{
    use ProcessesMenuItems;

    protected Collection $menus;
    protected ?string $headingLabel = null;
    protected ?string $headingIcon = null;
    protected $authorization = null;

    public function setHeadingLabel(string $label): self;
    public function setHeadingIcon(string $icon): self;
    public function setAuthorization(callable|string|bool $authorization): self;
    public function isAuthorized(): bool;
    public function getAuthorizationForBlade(): ?string;
    public function menus(): Collection;

    abstract public function build(): self;
    abstract protected function getMenuConfiguration(): array;
}
```

## Menu Item Structure

Individual menu items are created using the `MenuItem` class:

```php
(new MenuItem)
    ->setLabel(__('Users'))
    ->setUrl(route('security.users.index'))
    ->setVisible(fn () => Gate::allows('manage.users'))
    ->setTooltip(__('Manage users'))
    ->setDescription(__('View and manage user accounts'))
    ->setIcon('user')
    ->setTarget('_blank')    // Optional: open in new tab
    ->setType('form')        // Optional: render as form submission
```

### MenuItem Properties

- `label` — Display text
- `url` — Route or URL
- `visible` — Closure or boolean for visibility
- `tooltip` — Tooltip text on hover
- `description` — Longer description for accessibility
- `icon` — Icon name (Lucide icons via Flux)
- `target` — Link target (`_blank` for new window)
- `type` — `'link'` (default) or `'form'` (for logout, etc.)

## Authorization System

Authorization works at two levels:

### Section-Level Authorization

Set via `setAuthorization()` in the builder's `build()` method:

```php
// Gate string
$this->setAuthorization('access.user-management');

// Closure
$this->setAuthorization(fn () => Auth::check());

// Boolean
$this->setAuthorization(true);
```

### Item-Level Authorization

Set via `setVisible()` on individual `MenuItem` instances:

```php
->setVisible(fn () => Gate::allows('manage.users'))
```

The `<x-menu>` component checks both levels — the section must be authorized, then individual items are filtered by visibility.

## Menu Helper Function

The `menu()` helper function is defined in `support/menu.php`:

```php
function menu(string $builder): Builder|Menu
{
    return Action::make()->build($builder);
}
```

Usage:

```php
$menu = menu('user-management');
$menu->menus();              // Collection of menu items
$menu->getHeadingLabel();    // "User Management"
$menu->getHeadingIcon();     // "users"
$menu->isAuthorized();       // bool
```

## Customizing Menus

### Adding Items to an Existing Menu

Add a new factory method and reference it in `getMenuConfiguration()`:

```php
class UserManagement extends Base
{
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createUsersMenuItem(),
            fn () => $this->createRolesMenuItem(),
            fn () => $this->createNewFeatureMenuItem(), // Add here
        ];
    }

    private function createNewFeatureMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Permissions'))
            ->setUrl(route('admin.permissions.index'))
            ->setVisible(fn () => Gate::allows('manage.permissions'))
            ->setTooltip(__('Manage permissions'))
            ->setIcon('key');
    }
}
```

### Creating a New Menu Section

1. Create a new class extending `Base`:

```php
namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class Reports extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Reports'))
            ->setHeadingIcon('chart-bar')
            ->setAuthorization('access.reports');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    protected function getMenuConfiguration(): array
    {
        return [
            fn () => (new MenuItem)
                ->setLabel(__('Analytics'))
                ->setUrl(route('reports.analytics'))
                ->setVisible(fn () => Gate::allows('view.analytics'))
                ->setIcon('trending-up'),
        ];
    }
}
```

2. Register in `app/Actions/Builder/Menu.php`:

```php
'reports' => Reports::class,
```

3. Add the key to `config/menu.php` — as a `sections` entry (offered in the
   switcher) or a `globals`/`footer` entry (pinned). Set `setHeadingUrl()` on the
   builder so the switcher and breadcrumb use the right landing page:

```php
'sections' => ['administration', 'media-management', 'reports'],
```

That's the only wiring step — the sidebar and breadcrumbs both read `config/menu.php`.

## Menu Component

The `<x-menu>` component (`resources/views/components/menu.blade.php`) handles all rendering:

- Checks section authorization via `isAuthorized()` (string authorizations are evaluated through `Gate::allows()`)
- Renders **two sibling variants** per menu, switched purely by CSS:
  - `resources/views/components/menu/expanded.blade.php` — full sidebar (`.sidebar-expanded-only`)
  - `resources/views/components/menu/collapsed.blade.php` — icon rail (`.sidebar-collapsed-only`)
- Supports link items (default) and form items (for logout, etc.)
- Supports nested children via `<x-navlist-with-child>` (expanded) or flattened flyout entries (collapsed)

## Collapsible Sidebar & Menu Groups

### Desktop Icon Rail

On desktop (`lg+`), the sidebar toggles between the full sidebar and a narrow icon-only rail
via the toggle button next to the logo. Mobile keeps the existing stashable hamburger behavior —
the rail only applies at `lg+`.

**State persistence** uses a plain cookie named `sidebar_collapsed` (`1`/`0`, 1 year):

- Written by the Alpine store `$store.sidebar` (registered in `resources/js/app.js`)
- Read server-side in `resources/views/components/layouts/app/sidebar.blade.php` to render
  the `data-collapsed` attribute on `<flux:sidebar>` — so under `wire:navigate` every
  server-rendered response is already in the correct state (no flicker, unlike localStorage)
- Excluded from cookie encryption in `bootstrap/app.php`:
  `$middleware->encryptCookies(except: ['sidebar_collapsed'])` — without this,
  `request()->cookie('sidebar_collapsed')` returns `null` for the JS-written cookie

**CSS contract** (`resources/css/app.css`): elements marked `.sidebar-expanded-only` are hidden
in rail mode; `.sidebar-collapsed-only` elements are hidden everywhere *except* rail mode.
The rules are scoped to `[data-flux-sidebar][data-collapsed]` at `lg+`.

### Flyout Submenus (Rail Mode)

In rail mode, each menu group renders as a ghost icon button (the builder's heading icon) that
opens a right-positioned `<flux:dropdown>` flyout listing the group's items. Icon-only items
(heading-less builders like `sidebar`) get tippy tooltips via `data-tippy-content` — tooltips
are re-initialized after `livewire:navigated` in `app.js`.

The trigger button gets an accent highlight when the group contains the active page.

### Collapsible Menu Groups (Expanded Mode)

Groups with headings render as `<flux:navlist.group expandable>` — each group collapses and
expands independently (multi-open) with a chevron, using the published override at
`resources/views/flux/navlist/group.blade.php`. Groups default to expanded on page load.

### Best Practice: Always Set a Heading Icon

Menu builders **must** call `setHeadingIcon()` — the heading icon becomes the group's rail icon
in collapsed mode. Without it, the rail falls back to a generic `circle` icon.

## Best Practices

1. **Authorization** — Always use gates for menu authorization at both section and item levels
2. **Menu Organization** — Group related items in logical sections with clear headings
3. **Icons** — Use Lucide icon names consistently (via Flux/Blade Lucide Icons)
4. **Internationalization** — Always wrap labels with `__()`
5. **Performance** — Minimize database queries in menu builders; cache when appropriate

## Troubleshooting

### Menu Not Showing

1. Check section authorization gate exists and user has permission
2. Verify individual item `setVisible()` conditions
3. Ensure routes referenced in `setUrl()` exist
4. Check the builder key is registered in `Menu.php`

### Adding Menu to Sidebar

Register the builder in `app/Actions/Builder/Menu.php`, then add its key to
`config/menu.php` (`sections`, `globals`, or `footer`). Do **not** hand-edit the
sidebar layout — it renders whatever `config/menu.php` lists.

### Section Not Selectable / Wrong Section Highlighted

1. Confirm the key is in `config('menu.sections')` and the builder is authorized.
2. The switcher's landing comes from the builder's `setHeadingUrl()`, falling
   back to its first item — a section whose items are all sub-groups (url `#`)
   needs a heading URL or a first real leaf, or it will land on `#`.
3. Active detection is longest URL-prefix — ensure the section's item URLs share
   a distinct path segment from other sections.
