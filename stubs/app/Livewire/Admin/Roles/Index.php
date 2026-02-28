<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.roles.index', [
            'roles' => Role::paginate(10),
        ]);
    }
}
