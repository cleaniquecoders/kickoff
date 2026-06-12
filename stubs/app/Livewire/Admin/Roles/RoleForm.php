<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class RoleForm extends Component
{
    use AuthorizesRequests;

    public bool $showing = false;

    public ?string $editingUuid = null;

    public string $displayName = '';

    public string $description = '';

    public bool $isEnabled = true;

    public bool $isProtected = false;

    #[On('open-role-form')]
    public function open(?string $uuid = null): void
    {
        $this->resetValidation();
        $this->reset('displayName', 'description', 'isEnabled', 'isProtected');

        if ($uuid) {
            $role = Role::where('uuid', $uuid)->firstOrFail();
            $this->authorize('update', $role);

            $this->editingUuid = $uuid;
            $this->displayName = $role->display_name;
            $this->description = (string) $role->description;
            $this->isEnabled = (bool) $role->is_enabled;
            $this->isProtected = $role->isProtected();
        } else {
            $this->authorize('create', Role::class);
            $this->editingUuid = null;
        }

        $this->showing = true;
    }

    public function save(): void
    {
        $this->validate([
            'displayName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $editing = $this->editingUuid
            ? Role::where('uuid', $this->editingUuid)->firstOrFail()
            : null;

        $editing ? $this->updateRole($editing) : $this->createRole();

        $this->showing = false;
        $this->dispatch('role-saved');
    }

    public function close(): void
    {
        $this->showing = false;
    }

    public function render(): View
    {
        return view('livewire.admin.roles.role-form');
    }

    private function createRole(): void
    {
        $this->authorize('create', Role::class);

        $name = Str::slug($this->displayName);

        if ($name === '' || Role::where('name', $name)->exists()) {
            $this->addError('displayName', __('A role with this name already exists.'));

            return;
        }

        Role::create([
            'name' => $name,
            'display_name' => $this->displayName,
            'description' => $this->description ?: null,
            'guard_name' => 'web',
            'is_enabled' => $this->isEnabled,
        ]);

        $this->dispatch('toast', type: 'success', message: __('Role :role created.', ['role' => $this->displayName]));
    }

    private function updateRole(Role $role): void
    {
        $this->authorize('update', $role);

        // Name stays immutable — permissions and code reference it.
        $role->update([
            'display_name' => $this->displayName,
            'description' => $this->description ?: null,
            'is_enabled' => $role->isProtected() ? true : $this->isEnabled,
        ]);

        $this->dispatch('toast', type: 'success', message: __('Role :role updated.', ['role' => $this->displayName]));
    }
}
