<?php

declare(strict_types=1);

namespace App\Concerns;

trait InteractsWithLivewireConfirm
{
    public function confirm(string $title, string $message, string $component, string $listener, ...$params)
    {
        $this->dispatch(
            'displayConfirmation',
            $title,
            $message,
            $component,
            $listener,
            $params,
        )->to('confirm');
    }
}
