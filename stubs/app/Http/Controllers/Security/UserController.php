<?php

declare(strict_types=1);

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $sub = 'Manage users in the application';

        return view('security.users.index', compact('sub'));
    }

    public function show(string $uuid): RedirectResponse
    {
        $user = User::query()->where('uuid', $uuid)->firstOrFail();
        $this->authorize('view', $user);

        return redirect()->route('security.users.index', ['view' => $user->uuid]);
    }
}
