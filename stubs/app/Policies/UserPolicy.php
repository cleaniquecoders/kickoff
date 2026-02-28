<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response|bool
    {
        return $user->can('users.view.list');
    }

    public function view(User $user, User $model): Response|bool
    {
        return $user->can('users.view.profile');
    }

    public function create(User $user): Response|bool
    {
        return $user->can('users.create.account');
    }

    public function update(User $user, User $model): Response|bool
    {
        return $user->can('users.update.account') || $model->id === $user->id;
    }

    public function delete(User $user, User $model): Response|bool
    {
        if ($user->uuid === $model->uuid) {
            return false;
        }

        return $user->can('users.delete.account');
    }

    public function restore(User $user, User $model): Response|bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): Response|bool
    {
        return false;
    }
}
