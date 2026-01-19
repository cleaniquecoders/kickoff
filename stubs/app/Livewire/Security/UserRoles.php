<?php

namespace App\Livewire\Security;

use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UserRoles extends Component
{
    public User $user;

    public array $selectedRoles = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
    }

    #[Computed]
    public function roles()
    {
        return Role::whereNotIn('name', ['Superadmin', 'User'])
            ->where('is_enabled', true)
            ->get();
    }

    public function toggleRole(int $roleId): void
    {
        $this->authorize('update', $this->user);

        $role = Role::findOrFail($roleId);

        if (in_array($roleId, $this->selectedRoles)) {
            $this->user->removeRole($role);
            $this->selectedRoles = array_values(array_diff($this->selectedRoles, [$roleId]));
            $this->dispatch('toast', type: 'success', message: "Role '{$role->display_name}' removed.");
        } else {
            $this->user->assignRole($role);
            $this->selectedRoles[] = $roleId;
            $this->dispatch('toast', type: 'success', message: "Role '{$role->display_name}' assigned.");
        }
    }

    public function render()
    {
        return view('livewire.security.user-roles');
    }
}
