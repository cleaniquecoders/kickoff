<?php

namespace App\Livewire\Notifications;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Bell extends Component
{
    public function markAsRead(string $id): void
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    #[Computed]
    public function recentNotifications(): Collection
    {
        return Auth::user()->unreadNotifications()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
