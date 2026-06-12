<?php

declare(strict_types=1);

namespace App\Livewire\Security\Users;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageAccess extends Component
{
    use AuthorizesRequests;

    public bool $showing = false;

    public ?string $userUuid = null;

    #[On('open-user-access')]
    public function open(string $uuid): void
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $this->authorize('assignRoles', $user);

        $this->userUuid = $uuid;
        $this->showing = true;
    }

    #[Computed]
    public function user(): ?User
    {
        return $this->userUuid
            ? User::with(['roles', 'permissions'])->where('uuid', $this->userUuid)->first()
            : null;
    }

    #[Computed]
    public function assignableRoles(): Collection
    {
        return Role::where('is_enabled', true)
            ->orderBy('display_name')
            ->get()
            ->reject(fn (Role $role) => $role->name === 'superadmin' && ! auth()->user()->hasRole('superadmin'));
    }

    #[Computed]
    public function permissions(): Collection
    {
        return Permission::where('is_enabled', true)->get()->groupBy('module');
    }

    /**
     * Permission ids granted through the user's roles (read-only here).
     */
    #[Computed]
    public function inheritedPermissionIds(): Collection
    {
        return $this->user
            ? $this->user->getPermissionsViaRoles()->pluck('id')
            : collect();
    }

    /**
     * Map of inherited permission id => role names granting it.
     */
    #[Computed]
    public function inheritedVia(): Collection
    {
        if (! $this->user) {
            return collect();
        }

        return $this->user->roles
            ->flatMap(fn (Role $role) => $role->permissions->map(fn (Permission $permission) => [
                'permission_id' => $permission->id,
                'role' => $role->display_name ?? $role->name,
            ]))
            ->groupBy('permission_id')
            ->map(fn (Collection $entries) => $entries->pluck('role')->unique()->implode(', '));
    }

    public function toggleRole(string $roleName): void
    {
        $user = $this->user;
        $this->authorize('assignRoles', $user);

        $role = $this->assignableRoles->firstWhere('name', $roleName);

        if (! $role) {
            return;
        }

        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
            $this->dispatch('toast', type: 'success', message: __('Role :role removed.', ['role' => $role->display_name]));
        } else {
            $user->assignRole($roleName);
            $this->dispatch('toast', type: 'success', message: __('Role :role assigned.', ['role' => $role->display_name]));
        }

        unset($this->user, $this->inheritedPermissionIds, $this->inheritedVia);
        $this->dispatch('user-saved');
    }

    public function togglePermission(int $permissionId): void
    {
        $user = $this->user;
        $this->authorize('assignPermissions', $user);

        // Role-inherited permissions are managed on the role, not the user.
        if ($this->inheritedPermissionIds->contains($permissionId)) {
            return;
        }

        $permission = Permission::findOrFail($permissionId);

        if ($user->permissions->contains('id', $permissionId)) {
            $user->revokePermissionTo($permission);
            $this->dispatch('toast', type: 'success', message: __("Permission ':permission' revoked.", ['permission' => $permission->display_name ?? $permission->name]));
        } else {
            $user->givePermissionTo($permission);
            $this->dispatch('toast', type: 'success', message: __("Permission ':permission' granted.", ['permission' => $permission->display_name ?? $permission->name]));
        }

        unset($this->user);
    }

    public function close(): void
    {
        $this->showing = false;
    }

    public function render(): View
    {
        return view('livewire.security.users.manage-access');
    }
}
