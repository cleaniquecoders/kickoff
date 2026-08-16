<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function confirmPassword()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/ConfirmPassword', []);
    }

    public function forgotPassword()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/ForgotPassword', []);
    }

    public function login()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/Login', []);
    }

    public function register()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/Register', []);
    }

    public function resetPassword()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/ResetPassword', []);
    }

    public function twoFactorChallenge()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/TwoFactorChallenge', []);
    }

    public function verifyEmail()
    {
        // TODO: pass real props; Livewire component state must move here.
        return Inertia::render('auth/VerifyEmail', []);
    }
}
