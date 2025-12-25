# Sidebar System

This document explains the sidebar system implementation, including menu builders, authorization, and how to create and manage sidebar menus.

## Overview

The sidebar system provides a flexible, modular approach to building navigation menus with proper authorization checks. It uses a builder pattern to create menu items with consistent structure and authorization.

## Architecture

### Menu Builder Classes

The sidebar system consists of several menu builder classes located in `app/Actions/Builder/Menu/`:

- `Base.php` - Base menu builder with common functionality
- `Administration.php` - Admin panel menu items
- `Security.php` - Security-related menu items
- `Support.php` - Support and monitoring tools
- `Sidebar.php` - Main navigation menu
- `SidebarFooter.php` - Quick actions and user menu
- `Event.php` - Application-specific features (customizable)

### Base Menu Builder

The `Base` class provides core functionality for all menu builders:

```php
class Base implements Builder, Menu
{
    private Collection $menus;
    private ?string $headingLabel = null;
    private ?string $headingIcon = null;
    private $authorization = null;

    // Core methods
    public function setHeadingLabel(string $label): self
    public function setHeadingIcon(string $icon): self
    public function setAuthorization($authorization): self
    public function isAuthorized(): bool
    public function build(): self
}
```

## Menu Item Structure

### MenuItem Class

Individual menu items are created using the `MenuItem` class:

```php
(new MenuItem)
    ->setLabel(__('Users'))
    ->setUrl(route('admin.users.index'))
    ->setVisible(fn () => Gate::allows('manage.users'))
    ->setTooltip(__('Manage users'))
    ->setDescription(__('View and manage user accounts'))
    ->setIcon('users')
    ->setTarget('_blank') // Optional
    ->setType('form') // Optional
```

### MenuItem Properties

- `label` - Display text for the menu item
- `url` - Route or URL for the menu item
- `visible` - Closure or boolean for visibility check
- `tooltip` - Tooltip text on hover
- `description` - Longer description for accessibility
- `icon` - Icon class or name
- `target` - Link target (`_blank` for new window)
- `type` - Special type (`form` for logout, etc.)

## Authorization System

### Gate-Based Authorization

Each menu section and item can have authorization requirements:

#### Section Authorization

```php
$this->setAuthorization('access.admin-panel');
```

#### Item Authorization

```php
->setVisible(fn () => Gate::allows('manage.users'))
```

### Authorization Types

1. **Gate String**: Direct gate name

   ```php
   ->setAuthorization('access.admin-panel')
   ```

2. **Closure**: Custom logic

   ```php
   ->setAuthorization(fn () => auth()->check())
   ```

3. **Boolean**: Static authorization

   ```php
   ->setAuthorization(true)
   ```

## Menu Implementations

### Administration Menu

Admin panel menu for user, role, and settings management:

```php
class Administration extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Administration'))
             ->setHeadingIcon('settings')
             ->setAuthorization('access.superadmin');

        $this->menus = collect([
            (new MenuItem)
                ->setLabel(__('Users'))
                ->setUrl(route('admin.users.index'))
                ->setVisible(fn () => Gate::allows('manage.users'))
                ->setTooltip(__('Manage users'))
                ->setDescription(__('View and manage user accounts'))
                ->setIcon('users'),

            (new MenuItem)
                ->setLabel(__('Roles'))
                ->setUrl(route('admin.roles.index'))
                ->setVisible(fn () => Gate::allows('manage.roles'))
                ->setTooltip(__('Manage roles'))
                ->setDescription(__('Define and manage user roles'))
                ->setIcon('shield-check'),

            (new MenuItem)
                ->setLabel(__('Settings'))
                ->setUrl(route('admin.settings.index'))
                ->setVisible(fn () => Gate::allows('manage.settings'))
                ->setTooltip(__('System settings'))
                ->setDescription(__('Configure system-wide settings'))
                ->setIcon('cog'),
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
```

### Security Menu

Security and access control menu:

```php
class Security extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Security'))
             ->setHeadingIcon('shield')
             ->setAuthorization('access.security');

        $this->menus = collect([
            (new MenuItem)
                ->setLabel(__('Access Control'))
                ->setUrl(route('security.access-control.index'))
                ->setVisible(fn () => Gate::allows('manage.access-control'))
                ->setTooltip(__('Manage access control'))
                ->setDescription(__('Define and manage access control rules'))
                ->setIcon('lock'),

            (new MenuItem)
                ->setLabel(__('Audit Trail'))
                ->setUrl(route('security.audit-trail.index'))
                ->setVisible(fn () => Gate::allows('view.audit-logs'))
                ->setTooltip(__('View audit trails'))
                ->setDescription(__('Audit logs for security and activity tracking'))
                ->setIcon('scroll-text'),
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
```

### Support Menu

Development and monitoring tools:

```php
class Support extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Support & Monitoring'))
             ->setHeadingIcon('life-buoy')
             ->setAuthorization('access.admin-panel');

        $this->menus = collect([
            (new MenuItem)
                ->setLabel(__('Telescope'))
                ->setUrl(route('telescope'))
                ->setTarget('_blank')
                ->setVisible(fn () => Gate::allows('access.telescope'))
                ->setTooltip(__('View Telescope'))
                ->setDescription(__('Access application debugging using Laravel Telescope'))
                ->setIcon('bug'),

            (new MenuItem)
                ->setLabel(__('Horizon'))
                ->setUrl(route('horizon.index'))
                ->setTarget('_blank')
                ->setVisible(fn () => Gate::allows('access.horizon'))
                ->setTooltip(__('Manage queues'))
                ->setDescription(__('Access Laravel Horizon to monitor and manage queues'))
                ->setIcon('arrow-right-left'),
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
```

### Sidebar Menu

Main navigation sidebar:

```php
class Sidebar extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Navigation'))
             ->setHeadingIcon('menu')
             ->setAuthorization('access.dashboard');

        $this->menus = collect([
            (new MenuItem)
                ->setLabel(__('Dashboard'))
                ->setUrl(route('dashboard'))
                ->setVisible(fn () => Gate::allows('access.dashboard'))
                ->setTooltip(__('Dashboard'))
                ->setIcon('layout-dashboard')
                ->setDescription(__('Access to your dashboard.')),
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
```

### Sidebar Footer

Quick actions and user menu:

```php
class SidebarFooter extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Quick Actions'))
             ->setHeadingIcon('zap');

        $menuItems = [
            (new MenuItem)
                ->setLabel(__('Documentation'))
                ->setUrl('#')
                ->setTarget('_blank')
                ->setIcon('book-open')
                ->setDescription(__('View Documentation'))
                ->setVisible(fn () => Gate::allows('access.dashboard')),

            (new MenuItem)
                ->setLabel(__('Logout'))
                ->setUrl(route('logout'))
                ->setType('form')
                ->setIcon('log-out')
                ->setDescription(__('Sign out of your account'))
                ->setVisible(fn () => Gate::allows('access.dashboard')),
        ];

        $this->menus = collect($menuItems)
            ->map(fn (MenuItem $item) => $item->build()->toArray())
            ->filter(fn (array $item) => $item !== []);

        return $this;
    }
}
```

## Customizing Menus

### Adding New Menu Items

To add a new menu item to an existing menu:

```php
// In the appropriate menu class
(new MenuItem)
    ->setLabel(__('New Feature'))
    ->setUrl(route('feature.index'))
    ->setVisible(fn () => Gate::allows('access.feature'))
    ->setTooltip(__('Access new feature'))
    ->setDescription(__('Description of the new feature'))
    ->setIcon('star'),
```

### Creating New Menu Sections

1. **Create Menu Class**:

   ```php
   class CustomMenu extends Base
   {
       public function build(): self
       {
           $this->setHeadingLabel(__('Custom Section'))
                ->setHeadingIcon('puzzle-piece')
                ->setAuthorization('access.custom');

           $this->menus = collect([
               // Menu items here
           ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
               ->map(fn (MenuItem $menu) => $menu->build()->toArray());

           return $this;
       }
   }
   ```

2. **Register the Menu Builder**:

   Add your menu class to the `App\Actions\Builder\Menu` action class so it can be accessed via the `menu()` helper:

   ```php
   // In App\Actions\Builder\Menu
   public function build(string $builder): Builder|Menu
   {
       return match ($builder) {
           'sidebar' => (new Menu\Sidebar())->build(),
           'administration' => (new Menu\Administration())->build(),
           'security' => (new Menu\Security())->build(),
           'support' => (new Menu\Support())->build(),
           'custom' => (new Menu\CustomMenu())->build(), // Add your menu here
           default => throw new InvalidArgumentException("Unknown menu builder: {$builder}"),
       };
   }
   ```

3. **Use in Blade Templates**:

   ```blade
   <!-- Using the menu component -->
   <x-menu menu-builder="custom" />

   <!-- Or access directly -->
   @php
       $customMenu = menu('custom');
   @endphp
   ```

### Conditional Menu Display

Use the authorization system for conditional display:

```php
// Show only to administrators
->setAuthorization('access.admin-panel')

// Show only in development
->setAuthorization(fn () => app()->environment(['local', 'staging']))

// Show based on user properties
->setAuthorization(fn () => auth()->user()?->is_premium)

// Combine conditions
->setVisible(fn () =>
    Gate::allows('manage.users') &&
    auth()->user()?->hasVerifiedEmail()
)
```

## Menu Helper Function

The sidebar system provides a convenient `menu()` helper function to instantiate and build menu classes.

### Helper Function Implementation

The `menu()` helper function is defined in `support/menu.php`:

```php
<?php

use App\Actions\Builder\Menu as Action;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu;

if (! function_exists('menu')) {
    /**
     * Menu helper to build menus based on type.
     *
     * @param  string  $builder  See app/Actions/Builder/Menu.php for the available menus.
     * @return \CleaniqueCoders\Traitify\Contracts\Builder|\CleaniqueCoders\Traitify\Contracts\Menu
     */
    function menu(string $builder): Builder|Menu
    {
        return Action::make()->build($builder);
    }
}
```

This helper provides a clean, consistent interface for accessing menu builders throughout your application. It eliminates the need for manual instantiation and ensures all menus are built properly.

### Menu Helper Usage

```php
// Get a menu instance
$menu = menu('administration');  // Returns built Administration menu instance
$menu = menu('security');       // Returns built Security menu instance
$menu = menu('sidebar');        // Returns built Sidebar menu instance
```

The helper function automatically:

1. Instantiates the appropriate menu class
2. Calls the `build()` method
3. Returns the built menu instance

### Available Menu Methods

Once you have a menu instance, you can access these methods:

```php
$menu = menu('administration');

// Get menu data
$menu->menus()              // Array of menu items
$menu->getHeadingLabel()    // Menu section heading
$menu->getHeadingIcon()     // Menu section icon
$menu->isAuthorized()       // Check if user can access this menu section
$menu->getAuthorizationForBlade() // Get authorization rule for Blade @can directive
```

## Usage in Blade Templates

### Using the Menu Component

The recommended way to render menus is using the `<x-menu>` component:

```blade
<!-- In your sidebar layout -->
<flux:navlist variant="outline">
    <x-menu menu-builder="sidebar" />
    <x-menu menu-builder="administration" />
    <x-menu menu-builder="security" />
    <x-menu menu-builder="support" />
</flux:navlist>
```

### Menu Component Implementation

The `<x-menu>` component handles all the logic:

```blade
{{-- resources/views/components/menu.blade.php --}}
@props(['menuBuilder'])

@php
    $menu = menu($menuBuilder);
@endphp

@if ($menu->isAuthorized())
    @if ($menu->getAuthorizationForBlade())
        @can($menu->getAuthorizationForBlade())
            <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
                @foreach ($menu->menus() as $menuItem)
                    <flux:navlist.item
                        icon="{{ data_get($menuItem, 'icon') }}"
                        :href="data_get($menuItem, 'url')"
                        :current="data_get($menuItem, 'active')"
                        wire:navigate>
                        {{ data_get($menuItem, 'label') }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
        @endcan
    @else
        <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
            @foreach ($menu->menus() as $menuItem)
                <flux:navlist.item
                    icon="{{ data_get($menuItem, 'icon') }}"
                    :href="data_get($menuItem, 'url')"
                    :current="data_get($menuItem, 'active')"
                    wire:navigate>
                    {{ data_get($menuItem, 'label') }}
                </flux:navlist.item>
            @endforeach
        </flux:navlist.group>
    @endif
@endif
```

### Custom Menu Rendering

For custom menu rendering, you can access the menu instance directly:

```blade
@php
    $administrationMenu = menu('administration');
@endphp

@if($administrationMenu->isAuthorized())
    <div class="menu-section">
        <h3 class="menu-heading">
            <i class="icon-{{ $administrationMenu->getHeadingIcon() }}"></i>
            {{ $administrationMenu->getHeadingLabel() }}
        </h3>

        <ul class="menu-items">
            @foreach($administrationMenu->menus() as $item)
                <li class="menu-item">
                    <a href="{{ $item['url'] }}"
                       @if($item['target']) target="{{ $item['target'] }}" @endif
                       title="{{ $item['tooltip'] }}">
                        <i class="icon-{{ $item['icon'] }}"></i>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
```

## Menu Data Structure

Each menu item in the `menus()` array has the following structure:

```php
[
    'label' => 'Users',                    // Display text
    'url' => '/admin/users',               // Route or URL
    'icon' => 'users',                     // Icon class/name
    'tooltip' => 'Manage users',           // Tooltip text
    'description' => 'View and manage...',  // Longer description
    'target' => '_blank',                  // Link target (optional)
    'type' => 'form',                      // Special type (optional)
    'active' => false,                     // Current page indicator
    'children' => [...],                   // Child menu items (optional)
]
```

## Complete Implementation Example

Here's how the sidebar system works together in a complete implementation:

### 1. Sidebar Layout Template

```blade
{{-- resources/views/components/layouts/app/sidebar.blade.php --}}
@auth
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <x-menu menu-builder="sidebar" />
                <x-menu menu-builder="event" />
                <x-menu menu-builder="administration" />
                <x-menu menu-builder="security" />
                <x-menu menu-builder="support" />
            </flux:navlist>

            <flux:spacer />

            <!-- User menu and other footer content -->
        </flux:sidebar>

        {{ $slot }}
    </body>
@endauth
```

### 2. Menu Component

The reusable menu component handles all menu rendering logic:

```blade
{{-- resources/views/components/menu.blade.php --}}
@props(['menuBuilder'])

@php
    $menu = menu($menuBuilder);
@endphp

@if ($menu->isAuthorized())
    @if ($menu->getAuthorizationForBlade())
        @can($menu->getAuthorizationForBlade())
            <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
                @foreach ($menu->menus() as $menuItem)
                    <flux:navlist.item
                        icon="{{ data_get($menuItem, 'icon') }}"
                        :href="data_get($menuItem, 'url')"
                        :current="data_get($menuItem, 'active')"
                        wire:navigate>
                        {{ data_get($menuItem, 'label') }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
        @endcan
    @else
        <flux:navlist.group :heading="$menu->getHeadingLabel()" class="grid">
            @foreach ($menu->menus() as $menuItem)
                <flux:navlist.item
                    icon="{{ data_get($menuItem, 'icon') }}"
                    :href="data_get($menuItem, 'url')"
                    :current="data_get($menuItem, 'active')"
                    wire:navigate>
                    {{ data_get($menuItem, 'label') }}
                </flux:navlist.item>
            @endforeach
        </flux:navlist.group>
    @endif
@endif
```

### 3. Menu Builder Action

The main action class that routes menu requests:

```php
<?php
// app/Actions/Builder/Menu.php

namespace App\Actions\Builder;

use App\Actions\Builder\Menu\Administration;
use App\Actions\Builder\Menu\Security;
use App\Actions\Builder\Menu\Sidebar;
use App\Actions\Builder\Menu\Support;
use App\Actions\Builder\Menu\Event;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu;
use InvalidArgumentException;

class Menu
{
    public static function make(): self
    {
        return new self();
    }

    public function build(string $builder): Builder|Menu
    {
        return match ($builder) {
            'sidebar' => (new Sidebar())->build(),
            'event' => (new Event())->build(),
            'administration' => (new Administration())->build(),
            'security' => (new Security())->build(),
            'support' => (new Support())->build(),
            default => throw new InvalidArgumentException("Unknown menu builder: {$builder}"),
        };
    }
}
```

This architecture provides:

- **Clean separation of concerns**: Each menu section has its own class
- **Consistent interface**: All menus use the same helper function
- **Flexible authorization**: Both section-level and item-level permissions
- **Reusable components**: The menu component works for all menu types
- **Easy maintenance**: Adding new menus requires minimal changes

## Best Practices

### 1. Authorization

- Always use gates for menu authorization
- Check permissions at both menu and item levels
- Use descriptive gate names following the `action.context` pattern

### 2. Menu Organization

- Group related items in logical sections
- Use clear, descriptive labels
- Provide helpful tooltips and descriptions

### 3. Icons and Styling

- Use consistent icon sets
- Follow design system guidelines
- Ensure accessibility with proper ARIA labels

### 4. Performance

- Cache menu data when appropriate
- Use collections for efficient filtering
- Minimize database queries in menu builders

### 5. Internationalization

- Always use translation functions `__()`
- Provide translation keys for all text
- Consider RTL language support

### 6. Responsive Design

- Ensure menus work on mobile devices
- Consider collapsible sections
- Test touch interactions

## Advanced Features

### Dynamic Menu Items

Load menu items dynamically based on database or external data:

```php
public function build(): self
{
    $this->setHeadingLabel(__('Dynamic Section'))
         ->setAuthorization('access.dynamic');

    $dynamicItems = collect();

    // Load from database
    $modules = Module::where('enabled', true)->get();

    foreach ($modules as $module) {
        $dynamicItems->push(
            (new MenuItem)
                ->setLabel($module->name)
                ->setUrl(route('modules.show', $module))
                ->setVisible(fn () => Gate::allows("access.{$module->slug}"))
                ->setIcon($module->icon)
        );
    }

    $this->menus = $dynamicItems
        ->reject(fn (MenuItem $menu) => ! $menu->isVisible())
        ->map(fn (MenuItem $menu) => $menu->build()->toArray());

    return $this;
}
```

### Menu Caching

Cache menu data for performance:

```php
public function build(): self
{
    $cacheKey = 'sidebar.menu.' . auth()->id();

    $this->menus = Cache::remember($cacheKey, 300, function () {
        return collect([
            // Build menu items
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());
    });

    return $this;
}
```

### Menu Events

Dispatch events for menu interactions:

```php
// When menu is built
event(new MenuBuilt($this->menus));

// When menu item is clicked (in frontend)
event(new MenuItemClicked($menuItem, auth()->user()));
```

## Troubleshooting

### Menu Not Showing

1. Check authorization gates
2. Verify user permissions
3. Ensure route exists
4. Check visibility conditions

### Icons Not Displaying

1. Verify icon name/class
2. Check CSS/icon font loading
3. Ensure proper icon system integration

### Performance Issues

1. Add menu caching
2. Optimize database queries
3. Minimize authorization checks
4. Use eager loading for relationships

This sidebar system provides a flexible, secure, and maintainable way to build navigation menus while ensuring proper access control and user experience.
