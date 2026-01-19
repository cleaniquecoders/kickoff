<?php

namespace App\Livewire\Security\AccessControl;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.security.access-control.index', [
            'roles' => Role::paginate(10),
        ]);
    }
}
