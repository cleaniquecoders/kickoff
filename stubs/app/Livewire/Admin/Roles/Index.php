<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Concerns\InteractsWithLivewireConfirm;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use InteractsWithLivewireConfirm;
    use WithPagination;

    public function delete(string $uuid): void
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();
        $this->authorize('delete', $role);

        if ($role->isProtected()) {
            $this->dispatch('toast', type: 'error', message: __('Protected roles cannot be deleted.'));

            return;
        }

        if ($role->users()->exists()) {
            $this->dispatch('toast', type: 'error', message: __('Cannot delete a role that is assigned to users.'));

            return;
        }

        $this->confirm(
            __('Delete Role'),
            __('Are you sure you want to delete :role?', ['role' => $role->display_name]),
            'admin.roles.index',
            'performDelete',
            $uuid
        );
    }

    #[On('performDelete')]
    public function performDelete(array $params): void
    {
        $role = Role::where('uuid', $params[0])->firstOrFail();
        $this->authorize('delete', $role);

        if ($role->isProtected() || $role->users()->exists()) {
            return;
        }

        $name = $role->display_name;
        $role->delete();

        $this->dispatch('toast', type: 'success', message: __('Role :role deleted.', ['role' => $name]));
    }

    public function toggleEnabled(string $uuid): void
    {
        $role = Role::where('uuid', $uuid)->firstOrFail();
        $this->authorize('update', $role);

        if ($role->isProtected()) {
            $this->dispatch('toast', type: 'error', message: __('Protected roles cannot be disabled.'));

            return;
        }

        $role->update(['is_enabled' => ! $role->is_enabled]);

        $this->dispatch('toast', type: 'success', message: $role->is_enabled
            ? __('Role :role enabled.', ['role' => $role->display_name])
            : __('Role :role disabled.', ['role' => $role->display_name]));
    }

    #[On('role-saved')]
    public function refreshList(): void
    {
        // Re-render with fresh data.
    }

    public function render(): View
    {
        return view('livewire.admin.roles.index', [
            'roles' => Role::withCount('users')->paginate(10),
        ]);
    }
}
