# User Management

This document covers the admin user management module: user CRUD via flyout panels, account
status control, role and permission assignment, and bulk actions.

## Overview

User management lives at **Security → Users** (`/security/users`). The page hosts a single
Livewire component with search, filters, stats, and bulk actions. All forms and detail panels
open as **flyout slide-overs** (`<flux:modal variant="flyout">`) — there are no separate
create/edit/show pages.

## Components

| Component | View | Purpose |
|-----------|------|---------|
| `App\Livewire\Security\Users\Index` | `livewire/security/users/index.blade.php` | Table, search, filters, stats, row + bulk actions |
| `App\Livewire\Security\Users\UserForm` | `livewire/security/users/user-form.blade.php` | Create/edit flyout |
| `App\Livewire\Security\Users\ManageAccess` | `livewire/security/users/manage-access.blade.php` | Roles + direct permissions flyout |
| `App\Livewire\Admin\Roles\RoleForm` | `livewire/admin/roles/role-form.blade.php` | Role create/edit flyout |

Flyouts open via browser events dispatched from the page or table rows:

```blade
<flux:button x-on:click="$dispatch('open-user-form')">Add User</flux:button>
<flux:menu.item x-on:click="$dispatch('open-user-form', { uuid: '{{ $user->uuid }}' })">Edit</flux:menu.item>
<flux:menu.item x-on:click="$dispatch('open-user-access', { uuid: '{{ $user->uuid }}' })">Manage Access</flux:menu.item>
```

Components listen with `#[On('open-user-form')]` etc. and dispatch `user-saved` when done,
which the Index listens for to refresh.

## Account Status Model

Status is **derived**, never stored — see `App\Enums\UserStatus` and `User::status()`:

| Status | Condition | Meaning |
|--------|-----------|---------|
| `DELETED` | `deleted_at` set (soft delete) | Removed from the app; restorable |
| `SUSPENDED` | `suspended_at` set | Visible to admins, but sign-in is blocked |
| `UNVERIFIED` | `email_verified_at` is null | Has not verified their email |
| `ACTIVE` | none of the above | Normal account |

**Suspension vs. deletion**: suspension blocks access while keeping the account visible and
intact (use it for policy violations or offboarding-in-progress); deletion hides the account
entirely. Both are reversible.

Suspension is enforced by `App\Http\Middleware\EnsureUserIsNotSuspended` (appended to the
`web` group in `bootstrap/app.php`): the next request from a suspended user logs them out,
invalidates the session, and redirects to login. Suspended users also cannot be impersonated.

```php
$user->suspend();        // sets suspended_at
$user->unsuspend();      // clears it
$user->isSuspended();    // bool
User::suspended()->get();
User::active()->get();   // not suspended AND verified
```

## Invite Flow (No Admin-Typed Passwords)

Creating a user never asks the admin for a password. The account is created with a random
32-character password, and (by default) a **password reset link** is emailed so the user sets
their own. This doubles as the invite flow.

## Permissions

All abilities follow the `module.action.target` convention, seeded from
`config/access-control.php`:

| Permission | Grants |
|------------|--------|
| `users.view.list` | View the users index |
| `users.create.account` | Create users |
| `users.update.account` | Edit user details |
| `users.delete.account` | Soft-delete users |
| `users.restore.account` | Restore deleted users |
| `users.suspend.account` | Suspend / activate users |
| `users.assign.roles` | Toggle roles on a user |
| `users.assign.permissions` | Toggle direct permissions on a user |
| `users.send.password-reset` | Send password reset links |
| `users.send.verification` | Resend verification emails |

`UserPolicy` adds guards on top of the permissions:

- Nobody can delete or suspend **themselves**
- **Superadmin** accounts can only be managed by another superadmin
- Roles a user holds that the actor cannot assign (e.g. `superadmin`) are preserved on save

## Roles vs. Direct Permissions

The **Manage Access** flyout has two tabs:

- **Roles** — toggle role membership (instant save, toast feedback)
- **Permissions** — toggle *direct* permissions. Permissions inherited via a role appear
  checked but disabled with a "via {role}" hint — manage those on the role instead
  (Admin → Roles → Manage Permissions).

## Role Management

Roles (Admin → Roles) support create/edit via flyout, enable/disable, and delete:

- The internal `name` is slugged from the display name at creation and is **immutable**
  (permissions and code reference it)
- Roles seeded from `config/access-control.php` (`superadmin`, `administrator`, `user`) are
  **protected**: they cannot be deleted or disabled (`Role::isProtected()`)
- Roles assigned to users cannot be deleted
- Disabled roles cannot be assigned

## Convention: Confirmations Never Open From Flyouts

Destructive actions (delete, suspend, bulk delete) are only triggered from the row 3-dot
dropdown or the bulk action bar — never from inside a flyout. This keeps the global
`<livewire:confirm />` modal from stacking on top of a flyout panel. Flyouts contain only
save/cancel and toggle actions.

## Bulk Actions

Select rows via checkboxes (header checkbox selects the visible page). The bulk bar supports:

- **Assign role** — applies to all selected users the actor may manage; others are skipped
- **Delete** — confirmation first; self and protected accounts are skipped and reported

## Extending: Adding a Row Action

1. Add a method to `Security\Users\Index` that loads the user by uuid and authorizes a policy
   ability. For destructive actions, call `$this->confirm(...)` and handle the
   `#[On('perform...')]` listener (note: the listener receives its params as an array).
2. Add a `flux:menu.item` to the row dropdown, wrapped in `@can`.
3. Add the policy method + permission to `config/access-control.php` if a new ability is needed.
4. Cover it in `tests/Feature/Security/`.
