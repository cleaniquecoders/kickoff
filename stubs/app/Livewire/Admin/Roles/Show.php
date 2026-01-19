<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Role $role;

    public array $selectedPermissions = [];

    public function mount(string $uuid): void
    {
        $this->role = Role::with('permissions')->where('uuid', $uuid)->firstOrFail();
        $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
    }

    #[Computed]
    public function permissions()
    {
        return Permission::where('is_enabled', true)
            ->orderBy('module')
            ->orderBy('function')
            ->get()
            ->groupBy('module');
    }

    public function updatePermissions(): void
    {
        $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

        $this->role->syncPermissions($permissions);

        $this->dispatch('toast',
            type: 'success',
            message: 'Permissions updated successfully!',
            duration: 3000
        );
    }

    public function render()
    {
        return view('livewire.admin.roles.show');
    }
}
