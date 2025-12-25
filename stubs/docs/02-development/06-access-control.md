# Access Control System

This document explains the access control system implementation in the application, including permissions, roles, gates, and how to manage user access.

## Overview

The access control system is built on Laravel's authorization features and provides a flexible, role-based permission system with human-readable permission names and simplified gate management.

## Configuration

### Access Control Config (`config/access-control.php`)

The access control system is configured through the `access-control.php` configuration file:

```php
return [
    'enabled' => env('ACCESS_CONTROL_ENABLED', true),

    'roles' => [
        'superadmin' => 'Full system access (Dictator)',
        'administrator' => 'Handles administration and security related works.',
        'user' => 'Default user role, can create and participate in events.',
    ],

    'permissions' => [
        // Grouped permission structure
    ],

    'role_scope' => [
        // Role to permission mappings
    ],
];
```

## Permission Structure

### Grouped Permissions

Permissions are organized into logical modules with descriptive names:

```php
'permissions' => [
    'admin' => [
        'view.panel' => 'View Admin Panel',
        'manage.settings' => 'Manage System Settings',
        'access.telescope' => 'Access Telescope Debugging',
        'access.horizon' => 'Access Horizon Queue Monitor',
        'impersonate.users' => 'Impersonate Other Users',
    ],

    'users' => [
        'view.list' => 'View User List',
        'view.profile' => 'View User Profile',
        'create.account' => 'Create User Account',
        'update.account' => 'Update User Account',
        'delete.account' => 'Delete User Account',
    ],

    'roles' => [
        'view.list' => 'View Role List',
        'create.role' => 'Create New Role',
        'update.role' => 'Update Role Permissions',
        'delete.role' => 'Delete Role',
    ],

    'security' => [
        'manage.access-control' => 'Manage Access Control',
        'view.audit-logs' => 'View Audit Logs',
    ],

    'profile' => [
        'view.own' => 'View Own Profile',
        'update.own' => 'Update Own Profile',
    ],

    'notifications' => [
        'view.own' => 'View Own Notifications',
        'update.own' => 'Update Own Notification Settings',
        'mark.read' => 'Mark Notifications as Read',
    ],

    'dashboard' => [
        'access.user' => 'Access User Dashboard',
        'access.admin' => 'Access Admin Dashboard',
    ],
],
```

### Permission Naming Convention

- **Format**: `action.target` or `action.context`
- **Actions**: `view`, `create`, `update`, `delete`, `manage`, `access`, `mark`
- **Multi-word targets**: Use hyphens (e.g., `access-control`, `audit-logs`)
- **Self-service**: Use `.own` suffix for user's own resources

## Role Scopes

Define what permissions each role has:

```php
'role_scope' => [
    'superadmin' => '*', // All permissions

    'administrator' => [
        // Admin Panel Access
        'admin.view.panel',
        'admin.manage.settings',
        'admin.access.telescope',
        'admin.access.horizon',
        'admin.impersonate.users',

        // User Management
        'users.view.list',
        'users.view.profile',
        'users.create.account',
        'users.update.account',
        'users.delete.account',

        // Role Management
        'roles.view.list',
        'roles.create.role',
        'roles.update.role',
        'roles.delete.role',

        // Security
        'security.manage.access-control',
        'security.view.audit-logs',

        // Dashboard
        'dashboard.access.admin',
    ],

    'user' => [
        'dashboard.access.user',
        'profile.view.own',
        'profile.update.own',
        'notifications.view.own',
        'notifications.update.own',
        'notifications.mark.read',
    ],
],
```

## Gates System

### Gate Naming Convention

Gates follow the `action.context-of-gate` format for consistency and clarity:

```php
// Format: action.context-of-gate
'access.admin-panel'     // Access to admin panel
'manage.users'           // Manage users
'view.audit-logs'        // View audit logs
'impersonate.users'      // Impersonate users
'access.dashboard'       // Access dashboard
```

### Defined Gates

The `AdminServiceProvider` defines the following gates:

#### Main Access Gates

- `access.admin-panel` - Access to admin panel
- `access.dashboard` - Access to dashboard
- `access.superadmin` - Superadmin privileges

#### Management Gates

- `manage.users` - User management
- `manage.roles` - Role management
- `manage.settings` - Settings management
- `manage.access-control` - Access control management

#### View Gates

- `view.audit-logs` - View audit logs

#### Tool Access Gates

- `access.telescope` - Access Laravel Telescope
- `access.horizon` - Access Laravel Horizon
- `access.security` - Access security features

#### User Self-Service Gates

- `access.profile` - Access own profile
- `access.notifications` - Access own notifications
- `impersonate.users` - Impersonate other users

### Using Gates in Code

#### In Controllers

```php
// Check gate access
if (Gate::denies('manage.users')) {
    abort(403, 'Access denied');
}

// Or use middleware
Route::middleware('can:manage.users')->group(function () {
    // Protected routes
});
```

#### In Blade Views

```blade
@can('manage.users')
    <a href="{{ route('admin.users.index') }}">Manage Users</a>
@endcan

@can('access.admin-panel')
    <!-- Admin content -->
@endcan
```

#### In Menu Classes

```php
->setVisible(fn () => Gate::allows('manage.users'))
->setAuthorization('access.admin-panel')
```

## Database Structure

### Permissions Table

- `id` - Primary key
- `uuid` - Unique identifier
- `name` - Permission name (e.g., `admin.view.panel`)
- `module` - Module name (e.g., `Admin`)
- `function` - Human description (e.g., `View Admin Panel`)
- `guard_name` - Guard name (default: `web`)
- `is_enabled` - Enable/disable permission

### Roles Table

- `id` - Primary key
- `uuid` - Unique identifier
- `name` - Role name (e.g., `administrator`)
- `display_name` - Display name (e.g., `Administrator`)
- `description` - Role description
- `guard_name` - Guard name (default: `web`)
- `is_enabled` - Enable/disable role

## Seeding Data

### Running the Seeder

```bash
php artisan db:seed --class=AccessControlSeeder
```

### What Gets Seeded

1. **Roles**: From `config('access-control.roles')`
2. **Permissions**: From `config('access-control.permissions')`
3. **Role-Permission Mappings**: From `config('access-control.role_scope')`

### Seeder Logic

The `AccessControlSeeder` processes the configuration:

1. Creates roles with display names and descriptions
2. Creates permissions with module and function metadata
3. Maps permissions to roles based on role scopes
4. Handles wildcard (`*`) for superadmin access

## Usage Examples

### Checking Permissions in Controllers

```php
class UserController extends Controller
{
    public function index()
    {
        // Gate check
        $this->authorize('manage.users');

        // Or direct permission check
        if (!auth()->user()->can('users.view.list')) {
            abort(403);
        }

        return view('users.index');
    }

    public function create()
    {
        // Check specific permission for granular control
        if (!auth()->user()->can('users.create.account')) {
            abort(403);
        }

        return view('users.create');
    }
}
```

### Role Assignment

```php
// Assign role to user
$user = User::find(1);
$user->assignRole('administrator');

// Check if user has role
if ($user->hasRole('administrator')) {
    // User is an administrator
}

// Give specific permission
$user->givePermissionTo('users.view.list');

// Check specific permission
if ($user->can('users.create.account')) {
    // User can create accounts
}
```

## Adding New Permissions

### 1. Update Configuration

Add new permissions to `config/access-control.php`:

```php
'permissions' => [
    'reports' => [
        'generate.monthly' => 'Generate Monthly Reports',
        'export.data' => 'Export Report Data',
        'schedule.automated' => 'Schedule Automated Reports',
    ],
],
```

### 2. Update Role Scopes

Add permissions to appropriate roles:

```php
'role_scope' => [
    'administrator' => [
        // ... existing permissions
        'reports.generate.monthly',
        'reports.export.data',
        'reports.schedule.automated',
    ],
],
```

### 3. Add Gates (Optional)

For menu/UI usage, add simplified gates:

```php
// In AdminServiceProvider
Gate::define('manage.reports', function (User $user) {
    return $user->can('reports.generate.monthly');
});
```

### 4. Run Seeder

```bash
php artisan db:seed --class=AccessControlSeeder
```

## Best Practices

### 1. Permission Granularity

- Use specific permissions for fine-grained control
- Use gates for menu/UI authorization
- Check permissions in controllers for business logic

### 2. Naming Conventions

- Follow `action.target` format
- Use hyphens for multi-word targets
- Be descriptive but concise

### 3. Role Design

- Keep roles simple and role-based
- Use wildcard (`*`) sparingly (only for superadmin)
- Document role purposes clearly

### 4. Security Considerations

- Always check permissions in controllers
- Use middleware for route protection
- Validate permissions on both frontend and backend
- Regularly audit role assignments

### 5. Performance

- Cache permission checks where appropriate
- Use gates for repeated UI checks
- Minimize database queries in authorization

## Troubleshooting

### Permission Not Working

1. Check if permission exists in database
2. Verify user has the permission via role
3. Ensure permission name matches exactly
4. Clear cache if using permission caching

### Gate Not Found

1. Check gate is defined in `AdminServiceProvider`
2. Verify gate name follows convention
3. Ensure provider is registered

### Role Assignment Issues

1. Verify role exists in database
2. Check role is enabled (`is_enabled = true`)
3. Ensure proper role scope configuration

This access control system provides a flexible, maintainable way to manage user permissions while keeping the codebase clean and secure.
