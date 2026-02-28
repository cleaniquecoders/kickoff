<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AuditPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response|bool
    {
        return $user->can('security.view.audit-logs');
    }

    public function view(User $user, Audit $audit): Response|bool
    {
        return $user->can('security.view.audit-logs');
    }

    public function create(User $user): Response|bool
    {
        return false;
    }

    public function update(User $user, Audit $audit): Response|bool
    {
        return false;
    }

    public function delete(User $user, Audit $audit): Response|bool
    {
        return false;
    }

    public function restore(User $user, Audit $audit): Response|bool
    {
        return false;
    }

    public function forceDelete(User $user, Audit $audit): Response|bool
    {
        return false;
    }
}
