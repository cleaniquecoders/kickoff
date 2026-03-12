<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Impersonating extends Component
{
    public function render(): View
    {
        return view('components.impersonating');
    }
}
