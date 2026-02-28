<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class Impersonating extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('components.impersonating');
    }
}
