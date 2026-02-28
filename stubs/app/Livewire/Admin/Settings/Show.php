<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\NotificationSettings;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Show extends Component
{
    public string $section;

    public array $settings = [];

    public function mount(string $section): void
    {
        $this->authorize('manage.settings');

        $this->section = $section;
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $generalSettings = app(GeneralSettings::class);
        $mailSettings = app(MailSettings::class);
        $notificationSettings = app(NotificationSettings::class);

        $this->settings = [
            'general' => [
                'site_name' => $generalSettings->site_name,
            ],
            'email' => [
                'from_address' => $mailSettings->from_address,
                'from_name' => $mailSettings->from_name,
            ],
            'notifications' => [
                'enabled' => $notificationSettings->enabled,
                'channels' => $notificationSettings->channels,
            ],
        ];
    }

    public function saveSettings(): void
    {
        $this->authorize('manage.settings');

        try {
            if ($this->section === 'general') {
                $this->validate([
                    'settings.general.site_name' => 'required|string|max:255',
                ]);

                $settings = app(GeneralSettings::class);
                $settings->site_name = $this->settings['general']['site_name'];
                $settings->save();

            } elseif ($this->section === 'email') {
                $this->validate([
                    'settings.email.from_address' => 'required|email',
                    'settings.email.from_name' => 'required|string|max:255',
                ]);

                $settings = app(MailSettings::class);
                $settings->from_address = $this->settings['email']['from_address'];
                $settings->from_name = $this->settings['email']['from_name'];
                $settings->save();

            } elseif ($this->section === 'notifications') {
                $settings = app(NotificationSettings::class);
                $settings->enabled = $this->settings['notifications']['enabled'] ?? false;
                $settings->channels = $this->settings['notifications']['channels'] ?? [];
                $settings->save();
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

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.settings.show');
    }
}
