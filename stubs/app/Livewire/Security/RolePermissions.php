<?php

namespace App\Livewire\Security;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Attributes\Computed;
use Livewire\Component;

class RolePermissions extends Component
{
    public Role $role;

    public array $selectedPermissions = [];

    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
    }

    #[Computed]
    public function permissions()
    {
        return Permission::where('is_enabled', true)->get()->groupBy('module');
    }

    public function togglePermission(int $permissionId): void
    {
        $this->authorize('update', $this->role);

        $permission = Permission::findOrFail($permissionId);

        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->role->revokePermissionTo($permission);
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permissionId]));
            $this->dispatch('toast', type: 'success', message: "Permission '{$permission->display_name}' revoked.");
        } else {
            $this->role->givePermissionTo($permission);
            $this->selectedPermissions[] = $permissionId;
            $this->dispatch('toast', type: 'success', message: "Permission '{$permission->display_name}' granted.");
        }
    }

    public function toggleModule(string $module): void
    {
        $this->authorize('update', $this->role);

        $modulePermissions = $this->permissions->get($module, collect());
        $modulePermissionIds = $modulePermissions->pluck('id')->toArray();

        $allSelected = collect($modulePermissionIds)->every(fn ($id) => in_array($id, $this->selectedPermissions));

        if ($allSelected) {
            // Revoke all permissions in this module
            foreach ($modulePermissions as $permission) {
                $this->role->revokePermissionTo($permission);
            }
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $modulePermissionIds));
            $this->dispatch('toast', type: 'success', message: "All permissions in '{$module}' revoked.");
        } else {
            // Grant all permissions in this module
            foreach ($modulePermissions as $permission) {
                if (! in_array($permission->id, $this->selectedPermissions)) {
                    $this->role->givePermissionTo($permission);
                    $this->selectedPermissions[] = $permission->id;
                }
            }
            $this->dispatch('toast', type: 'success', message: "All permissions in '{$module}' granted.");
        }
    }

    public function render()
    {
        return view('livewire.security.role-permissions');
    }
}
