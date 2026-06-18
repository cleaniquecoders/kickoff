<?php

declare(strict_types=1);

namespace App\Livewire\Security\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class UserForm extends Component
{
    use AuthorizesRequests;

    public bool $showing = false;

    public ?string $editingUuid = null;

    public string $name = '';

    public string $email = '';

    /** @var array<int, string> */
    public array $roles = [];

    public bool $sendPasswordSetupLink = true;

    #[On('open-user-form')]
    public function open(?string $uuid = null): void
    {
        $this->resetValidation();
        $this->reset('name', 'email', 'roles', 'sendPasswordSetupLink');

        if ($uuid) {
            $user = User::where('uuid', $uuid)->firstOrFail();
            $this->authorize('update', $user);

            $this->editingUuid = $uuid;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->roles = $user->roles->pluck('name')->toArray();
        } else {
            $this->authorize('create', User::class);
            $this->editingUuid = null;
        }

        $this->showing = true;
    }

    #[Computed]
    public function assignableRoles(): Collection
    {
        return Role::where('is_enabled', true)
            ->orderBy('display_name')
            ->get()
            ->reject(fn (Role $role) => $role->name === 'superadmin' && ! auth()->user()->hasRole('superadmin'));
    }

    public function save(): void
    {
        $editing = $this->editingUuid
            ? User::where('uuid', $this->editingUuid)->firstOrFail()
            : null;

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($editing?->id),
            ],
            'roles' => ['array'],
            'roles.*' => [Rule::in($this->assignableRoles->pluck('name'))],
        ]);

        $editing ? $this->updateUser($editing) : $this->createUser();

        $this->showing = false;
        $this->dispatch('user-saved');
    }

    public function close(): void
    {
        $this->showing = false;
    }

    public function render(): View
    {
        return view('livewire.security.users.user-form');
    }

    private function createUser(): void
    {
        $this->authorize('create', User::class);

        // No admin-typed passwords — the user sets their own via the reset link.
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Str::password(32),
        ]);

        $user->assignRole(array_unique([...$this->roles, 'user']));

        if ($this->sendPasswordSetupLink) {
            Password::sendResetLink(['email' => $user->email]);
        }

        $this->dispatch('toast', type: 'success', message: __(':name created.', ['name' => $user->name]));
    }

    private function updateUser(User $user): void
    {
        $this->authorize('update', $user);

        $emailChanged = $user->email !== $this->email;

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }

        if (auth()->user()->can('assignRoles', $user)) {
            $protected = $user->roles->pluck('name')
                ->diff($this->assignableRoles->pluck('name'))
                ->all();

            $user->syncRoles(array_unique([...$this->roles, ...$protected]));
        }

        $message = $emailChanged
            ? __(':name updated. Email changed — verification reset.', ['name' => $user->name])
            : __(':name updated.', ['name' => $user->name]);

        $this->dispatch('toast', type: 'success', message: $message);
    }
}
