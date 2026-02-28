<?php

declare(strict_types=1);

namespace App\Concerns;

trait InteractsWithLivewireAlert
{
    public function alert(string $title, string $message): void
    {
        $this->dispatch(
            'displayAlert', $title, $message
        )->to('alert');
    }
}
