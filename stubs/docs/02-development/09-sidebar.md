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
- Navigation using `<x-menu>` components for each menu builder
- Desktop and mobile user menu dropdowns
- Toast notification system
- Session message to toast conversion
- `@auth` / `@else` guard for unauthenticated users

### Menu Builders in the Sidebar

The sidebar renders these menu builders in order:

```blade
<flux:navlist variant="outline">
    <x-menu menu-builder="sidebar" />
    <x-menu menu-builder="user-management" />
    <x-menu menu-builder="media-management" />
    <x-menu menu-builder="settings" />
    <x-menu menu-builder="audit-monitoring" />
</flux:navlist>
```

### Menu Builder Classes

Located in `app/Actions/Builder/Menu/`:

| Class | Builder Key | Heading | Items |
|-------|-------------|---------|-------|
| `Base.php` | — | — | Abstract base class |
| `Sidebar.php` | `sidebar` | *(none)* | Dashboard, Notifications |
| `UserManagement.php` | `user-management` | User Management | Users, Roles |
| `MediaManagement.php` | `media-management` | Media | Media Library |
| `Settings.php` | `settings` | Settings | General |
| `AuditMonitoring.php` | `audit-monitoring` | Audit & Monitoring | Audit Trail, Telescope, Horizon |

### Menu Router

`app/Actions/Builder/Menu.php` routes builder keys to classes:

```php
public function build(string $builder): Builder|ContractsMenu
{
    $class = match ($builder) {
        'sidebar' => Sidebar::class,
        'user-management' => UserManagement::class,
        'media-management' => MediaManagement::class,
        'settings' => Settings::class,
        'audit-monitoring' => AuditMonitoring::class,
        default => Sidebar::class,
    };

    return (new $class)->build();
}
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

3. Add to the sidebar layout:

```blade
<x-menu menu-builder="reports" />
```

## Menu Component

The `<x-menu>` component (`resources/views/components/menu.blade.php`) handles all rendering:

- Checks section authorization via `isAuthorized()` and `getAuthorizationForBlade()`
- Renders items as `<flux:navlist.item>` within a `<flux:navlist.group>`
- Supports link items (default) and form items (for logout, etc.)
- Supports nested children via `<x-navlist-with-child>`

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

Always add `<x-menu menu-builder="your-key" />` inside the `<flux:navlist>` block in `resources/views/components/layouts/app/sidebar.blade.php`.
