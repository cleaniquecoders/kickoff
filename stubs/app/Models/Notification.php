<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification as Model;

class Notification extends Model
{
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('read_at', 'asc');
    }
}
