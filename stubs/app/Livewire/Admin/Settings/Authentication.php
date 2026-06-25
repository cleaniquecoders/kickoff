<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Settings\AuthenticationSettings;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Admin > Settings > Authentication.
 *
 * Manages DB-stored authentication options. Currently the public registration
 * toggle; AppServiceProvider lays the saved value over config('admin.*') so the
 * login screen and Fortify routes pick it up.
 */
#[Layout('components.layouts.app')]
class Authentication extends Component
{
    public bool $publicRegistrationEnabled = true;

    public function mount(): void
    {
        $this->authorize('manage.settings');

        $this->publicRegistrationEnabled = app(AuthenticationSettings::class)->public_registration_enabled;
    }

    public function save(): void
    {
        $this->authorize('manage.settings');

        $settings = app(AuthenticationSettings::class);
        $settings->public_registration_enabled = $this->publicRegistrationEnabled;
        $settings->save();

        // Keep the live config in sync for the rest of this request.
        config(['admin.public_registration' => $this->publicRegistrationEnabled]);

        $this->dispatch('toast',
            type: 'success',
            message: __('Authentication settings saved successfully!'),
            duration: 3000,
        );
    }

    public function render(): View
    {
        return view('livewire.admin.settings.authentication');
    }
}
