<?php

declare(strict_types=1);

namespace App\Livewire\Security;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UserPanel extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public array $selectedRoles = [];

    public function mount(string $uuid): void
    {
        $this->user = User::query()->where('uuid', $uuid)->firstOrFail();
        $this->authorize('view', $this->user);

        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
    }

    #[Computed]
    public function roles(): Collection
    {
        return Role::query()
            ->whereNotIn('name', ['Superadmin', 'User'])
            ->where('is_enabled', true)
            ->get();
    }

    public function canUpdate(): bool
    {
        return auth()->user()->can('update', $this->user);
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->user);

        $data = $this->validate();

        $this->user->fill($data)->save();
        $this->user->refresh();

        $this->dispatch('toast', type: 'success', message: __('User details saved.'));
    }

    public function toggleRole(int $roleId): void
    {
        $this->authorize('update', $this->user);

        $role = Role::findOrFail($roleId);

        if (in_array($roleId, $this->selectedRoles, true)) {
            $this->user->removeRole($role);
            $this->selectedRoles = array_values(array_diff($this->selectedRoles, [$roleId]));
            $this->dispatch('toast', type: 'success', message: "Role '{$role->display_name}' removed.");
        } else {
            $this->user->assignRole($role);
            $this->selectedRoles[] = $roleId;
            $this->dispatch('toast', type: 'success', message: "Role '{$role->display_name}' assigned.");
        }
    }

    public function render(): View
    {
        return view('livewire.security.user-panel');
    }
}
