<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as Model;

class Notification extends Model
{
    public function scopeForUser(\Illuminate\Database\Eloquent\Builder $query, User $user): \Illuminate\Database\Eloquent\Builder
    {
        return $query
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('read_at', 'asc');
    }
}
