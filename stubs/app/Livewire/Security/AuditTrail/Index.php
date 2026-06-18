<?php

declare(strict_types=1);

namespace App\Livewire\Security\AuditTrail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?string $detailUuid = null;

    public bool $showDetail = false;

    public int $detailKey = 0;

    public function mount(): void
    {
        $this->authorize('viewAny', config('audit.implementation'));
    }

    /**
     * Open the read-only audit detail flyout.
     */
    public function openDetail(string $uuid): void
    {
        $audit = config('audit.implementation')::whereUuid($uuid)->firstOrFail();
        $this->authorize('view', $audit);

        $this->detailUuid = $uuid;
        $this->detailKey++;
        $this->showDetail = true;
    }

    /**
     * The audit record shown in the detail flyout.
     */
    #[Computed]
    public function selectedAudit(): ?Model
    {
        if (! $this->detailUuid) {
            return null;
        }

        return config('audit.implementation')::with('user')->whereUuid($this->detailUuid)->first();
    }

    public function render(): View
    {
        return view('livewire.security.audit-trail.index', [
            'audits' => config('audit.implementation')::with('user')->latest()->paginate(20),
        ]);
    }
}
