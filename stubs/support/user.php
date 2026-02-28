<?php

declare(strict_types=1);

use App\Models\User;

if (! function_exists('user')) {
    function user(): ?User
    {
        return auth()->user();
    }
}
