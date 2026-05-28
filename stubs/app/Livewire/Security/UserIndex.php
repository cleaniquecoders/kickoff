<?php

declare(strict_types=1);

namespace App\Livewire\Security;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public bool $showPanel = false;

    public ?string $selectedUuid = null;

    public int $panelKey = 0;

    #[Url(as: 'view', except: '')]
    public string $deepLinkView = '';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);

        if ($this->deepLinkView !== '') {
            $this->openShow($this->deepLinkView);
            $this->deepLinkView = '';
        }
    }

    public function openShow(string $uuid): void
    {
        $user = User::query()->where('uuid', $uuid)->firstOrFail();
        $this->authorize('view', $user);

        $this->selectedUuid = $uuid;
        $this->panelKey++;
        $this->showPanel = true;
    }

    #[On('user-panel-closed')]
    public function onPanelClosed(): void
    {
        $this->showPanel = false;
        $this->selectedUuid = null;
    }

    public function render(): View
    {
        return view('livewire.security.user-index', [
            'users' => User::query()
                ->with('roles')
                ->orderBy('name')
                ->paginate(15),
            'totalUsers' => User::query()->count(),
            'activeToday' => User::query()->whereDate('updated_at', today())->count(),
            'withRoles' => User::query()->has('roles')->count(),
            'newThisMonth' => User::query()->whereMonth('created_at', now()->month)->count(),
        ]);
    }
}
