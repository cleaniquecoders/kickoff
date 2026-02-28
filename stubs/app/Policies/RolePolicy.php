<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response|bool
    {
        return $user->can('roles.view.list');
    }

    public function view(User $user, Role $role): Response|bool
    {
        return $user->can('roles.view.list');
    }

    public function create(User $user): Response|bool
    {
        return $user->can('roles.create.role');
    }

    public function update(User $user, Role $role): Response|bool
    {
        return $user->can('roles.update.role');
    }

    public function delete(User $user, Role $role): Response|bool
    {
        return $user->can('roles.delete.role');
    }

    public function restore(User $user, Role $role): Response|bool
    {
        return false;
    }

    public function forceDelete(User $user, Role $role): Response|bool
    {
        return false;
    }
}
