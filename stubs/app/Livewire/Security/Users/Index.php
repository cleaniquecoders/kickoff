<?php

declare(strict_types=1);

namespace App\Livewire\Security\Users;

use App\Concerns\InteractsWithLivewireConfirm;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use InteractsWithLivewireConfirm;
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $roleFilter = '';

    #[Url]
    public string $statusFilter = '';

    /** @var array<int, string> */
    public array $selected = [];

    public bool $selectPage = false;

    public string $bulkRole = '';

    public ?string $detailUuid = null;

    public bool $showDetail = false;

    public int $detailKey = 0;

    /**
     * Open the user detail flyout for the given user.
     */
    public function openDetail(string $uuid): void
    {
        $this->detailUuid = $uuid;
        $this->detailKey++;
        $this->showDetail = true;
    }

    /**
     * The user shown in the detail flyout.
     */
    #[Computed]
    public function selectedUser(): ?User
    {
        if (! $this->detailUuid) {
            return null;
        }

        return User::withTrashed()->with('roles')->where('uuid', $this->detailUuid)->first();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedPage(): void
    {
        $this->clearSelection();
    }

    public function updatedSelectPage(bool $value): void
    {
        $this->selected = $value
            ? $this->users->pluck('uuid')->map(fn ($uuid) => (string) $uuid)->toArray()
            : [];
    }

    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectPage = false;
        $this->bulkRole = '';
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return $this->usersQuery()->paginate(15);
    }

    #[Computed]
    public function roles(): Collection
    {
        return Role::where('is_enabled', true)->orderBy('display_name')->get();
    }

    #[Computed]
    public function assignableRoles(): Collection
    {
        return $this->roles->reject(
            fn (Role $role) => $role->name === 'superadmin' && ! auth()->user()->hasRole('superadmin')
        );
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::active()->count(),
            'suspended' => User::suspended()->count(),
            'deleted' => User::onlyTrashed()->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Row Actions
    |--------------------------------------------------------------------------
    */

    public function delete(string $uuid): void
    {
        $user = $this->findUser($uuid);
        $this->authorize('delete', $user);

        $this->confirm(
            __('Delete User'),
            __('Are you sure you want to delete :name? The account can be restored later.', ['name' => $user->name]),
            'security.users.index',
            'performDelete',
            $uuid
        );
    }

    #[On('performDelete')]
    public function performDelete(array $params): void
    {
        $user = $this->findUser($params[0]);
        $this->authorize('delete', $user);

        $user->delete();
        $this->clearSelection();
        $this->dispatch('toast', type: 'success', message: __(':name deleted.', ['name' => $user->name]));
    }

    public function restore(string $uuid): void
    {
        $user = $this->findUser($uuid, withTrashed: true);
        $this->authorize('restore', $user);

        $user->restore();
        $this->dispatch('toast', type: 'success', message: __(':name restored.', ['name' => $user->name]));
    }

    public function suspend(string $uuid): void
    {
        $user = $this->findUser($uuid);
        $this->authorize('suspend', $user);

        $this->confirm(
            __('Suspend User'),
            __(':name will no longer be able to sign in. Continue?', ['name' => $user->name]),
            'security.users.index',
            'performSuspend',
            $uuid
        );
    }

    #[On('performSuspend')]
    public function performSuspend(array $params): void
    {
        $user = $this->findUser($params[0]);
        $this->authorize('suspend', $user);

        $user->suspend();
        $this->dispatch('toast', type: 'success', message: __(':name suspended.', ['name' => $user->name]));
    }

    public function activate(string $uuid): void
    {
        $user = $this->findUser($uuid);
        $this->authorize('suspend', $user);

        $user->unsuspend();
        $this->dispatch('toast', type: 'success', message: __(':name activated.', ['name' => $user->name]));
    }

    public function sendPasswordResetLink(string $uuid): void
    {
        $user = $this->findUser($uuid);
        $this->authorize('sendPasswordReset', $user);

        $status = Password::sendResetLink(['email' => $user->email]);

        $status === Password::RESET_LINK_SENT
            ? $this->dispatch('toast', type: 'success', message: __('Password reset link sent to :email.', ['email' => $user->email]))
            : $this->dispatch('toast', type: 'error', message: __($status));
    }

    public function resendVerification(string $uuid): void
    {
        $user = $this->findUser($uuid);
        $this->authorize('sendVerification', $user);

        if ($user->hasVerifiedEmail()) {
            $this->dispatch('toast', type: 'warning', message: __(':name is already verified.', ['name' => $user->name]));

            return;
        }

        $user->sendEmailVerificationNotification();
        $this->dispatch('toast', type: 'success', message: __('Verification email sent to :email.', ['email' => $user->email]));
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Actions
    |--------------------------------------------------------------------------
    */

    public function bulkDelete(): void
    {
        $this->confirm(
            __('Delete Selected Users'),
            __('Delete :count selected user(s)? Protected accounts will be skipped.', ['count' => count($this->selected)]),
            'security.users.index',
            'performBulkDelete'
        );
    }

    #[On('performBulkDelete')]
    public function performBulkDelete(): void
    {
        $deleted = 0;
        $skipped = 0;

        foreach (User::whereIn('uuid', $this->selected)->get() as $user) {
            if (auth()->user()->cannot('delete', $user)) {
                $skipped++;

                continue;
            }

            $user->delete();
            $deleted++;
        }

        $this->clearSelection();

        $message = trans_choice(':count user deleted.|:count users deleted.', $deleted, ['count' => $deleted]);

        if ($skipped > 0) {
            $message .= ' '.__(':count skipped.', ['count' => $skipped]);
        }

        $this->dispatch('toast', type: $deleted > 0 ? 'success' : 'warning', message: $message);
    }

    public function bulkAssignRole(): void
    {
        $this->validate([
            'bulkRole' => ['required', 'exists:roles,name'],
        ]);

        $role = $this->assignableRoles->firstWhere('name', $this->bulkRole);

        if (! $role) {
            $this->dispatch('toast', type: 'error', message: __('Selected role cannot be assigned.'));

            return;
        }

        $assigned = 0;
        $skipped = 0;

        foreach (User::whereIn('uuid', $this->selected)->get() as $user) {
            if (auth()->user()->cannot('assignRoles', $user)) {
                $skipped++;

                continue;
            }

            $user->assignRole($role);
            $assigned++;
        }

        $this->clearSelection();

        $message = __('Role :role assigned to :count user(s).', ['role' => $role->display_name, 'count' => $assigned]);

        if ($skipped > 0) {
            $message .= ' '.__(':count skipped.', ['count' => $skipped]);
        }

        $this->dispatch('toast', type: $assigned > 0 ? 'success' : 'warning', message: $message);
    }

    #[On('user-saved')]
    public function refreshList(): void
    {
        unset($this->users, $this->stats);
    }

    public function render(): View
    {
        return view('livewire.security.users.index');
    }

    private function usersQuery(): Builder
    {
        return User::query()
            ->with('roles')
            ->when($this->statusFilter === 'deleted', fn (Builder $query) => $query->onlyTrashed())
            ->when($this->statusFilter === 'active', fn (Builder $query) => $query->active())
            ->when($this->statusFilter === 'suspended', fn (Builder $query) => $query->suspended())
            ->when($this->statusFilter === 'unverified', fn (Builder $query) => $query->whereNull('email_verified_at'))
            ->when($this->search, fn (Builder $query) => $query->where(
                fn (Builder $query) => $query
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->roleFilter, fn (Builder $query) => $query->whereHas(
                'roles',
                fn ($query) => $query->where('name', $this->roleFilter)
            ))
            ->orderBy('name');
    }

    private function findUser(string $uuid, bool $withTrashed = false): User
    {
        return User::query()
            ->when($withTrashed || $this->statusFilter === 'deleted', fn (Builder $query) => $query->withTrashed())
            ->where('uuid', $uuid)
            ->firstOrFail();
    }
}
