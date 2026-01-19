<?php

namespace App\Livewire\Admin\Settings;

use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Show extends Component
{
    public string $section;

    public array $settings = [];

    public function mount(string $section): void
    {
        $this->section = $section;
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $this->settings = [
            'general' => [
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
            ],
            'email' => [
                'mail_mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_password' => config('mail.mailers.smtp.password'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_address' => config('mail.from.address'),
                'mail_from_name' => config('mail.from.name'),
            ],
            'notifications' => [
                'enabled' => env('NOTIFICATIONS_ENABLED', true),
                'channels' => explode(',', env('NOTIFICATIONS_CHANNELS', 'mail,database')),
            ],
        ];
    }

    public function saveSettings(): void
    {
        try {
            if ($this->section === 'general') {
                $this->validate([
                    'settings.general.app_name' => 'required|string|max:255',
                    'settings.general.app_env' => 'required|in:local,development,staging,production',
                ]);

                update_env_multiple([
                    'APP_NAME' => $this->settings['general']['app_name'],
                    'APP_ENV' => $this->settings['general']['app_env'],
                    'APP_DEBUG' => $this->settings['general']['app_debug'] ?? false,
                ]);

            } elseif ($this->section === 'email') {
                $this->validate([
                    'settings.email.mail_mailer' => 'required|in:smtp,sendmail,mailgun,ses,log',
                    'settings.email.mail_host' => 'nullable|string|max:255',
                    'settings.email.mail_port' => 'nullable|integer|min:1|max:65535',
                    'settings.email.mail_username' => 'nullable|string|max:255',
                    'settings.email.mail_encryption' => 'nullable|in:tls,ssl',
                    'settings.email.mail_from_address' => 'required|email',
                    'settings.email.mail_from_name' => 'required|string|max:255',
                ]);

                update_env_multiple([
                    'MAIL_MAILER' => $this->settings['email']['mail_mailer'],
                    'MAIL_HOST' => $this->settings['email']['mail_host'] ?? '',
                    'MAIL_PORT' => $this->settings['email']['mail_port'] ?? 587,
                    'MAIL_USERNAME' => $this->settings['email']['mail_username'] ?? '',
                    'MAIL_PASSWORD' => $this->settings['email']['mail_password'] ?? '',
                    'MAIL_ENCRYPTION' => $this->settings['email']['mail_encryption'] ?? 'tls',
                    'MAIL_FROM_ADDRESS' => $this->settings['email']['mail_from_address'],
                    'MAIL_FROM_NAME' => $this->settings['email']['mail_from_name'],
                ]);

            } elseif ($this->section === 'notifications') {
                update_env_multiple([
                    'NOTIFICATIONS_ENABLED' => $this->settings['notifications']['enabled'] ?? false,
                    'NOTIFICATIONS_CHANNELS' => implode(',', $this->settings['notifications']['channels'] ?? []),
                ]);
            }

            $this->dispatch('toast',
                type: 'success',
                message: ucfirst($this->section).' settings saved successfully!',
                duration: 3000
            );

            $this->loadSettings();

        } catch (ValidationException $e) {
            $this->dispatch('toast',
                type: 'error',
                message: 'Please fix the validation errors.',
                duration: 5000
            );
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast',
                type: 'error',
                message: 'Failed to save settings: '.$e->getMessage(),
                duration: 5000
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.show');
    }
}
