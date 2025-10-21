<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return auth()->user()->can('users.view.list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        return auth()->user()->can('users.view.profile');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return auth()->user()->can('users.create.account');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        return auth()->user()->can('users.update.account') || $model->id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        if ($user->uuid === $model->uuid) {
            return false;
        }

        return $user->can('users.delete.account');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        return false;
    }
}
