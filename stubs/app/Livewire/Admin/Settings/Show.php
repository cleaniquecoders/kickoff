<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Settings;

use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class Show extends Component
{
    public string $section;

    public array $settings = [];

    public string $testRecipient = '';

    public function mount(string $section): void
    {
        $this->authorize('manage.settings');

        $this->section = $section;
        $this->loadSettings();
        $this->testRecipient = (string) (auth()->user()?->email ?? '');
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
                'mailer' => $mailSettings->mailer,
                'host' => $mailSettings->host,
                'port' => $mailSettings->port,
                'username' => $mailSettings->username,
                'password' => $mailSettings->password,
                'encryption' => $mailSettings->encryption,
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
                    'settings.email.mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark,log,array',
                    'settings.email.host' => 'nullable|string|max:255',
                    'settings.email.port' => 'nullable|integer|min:1|max:65535',
                    'settings.email.username' => 'nullable|string|max:255',
                    'settings.email.password' => 'nullable|string|max:255',
                    'settings.email.encryption' => 'nullable|in:tls,ssl',
                    'settings.email.from_address' => 'required|email',
                    'settings.email.from_name' => 'required|string|max:255',
                ]);

                $settings = app(MailSettings::class);
                $settings->mailer = $this->settings['email']['mailer'];
                $settings->host = (string) ($this->settings['email']['host'] ?? '');
                $settings->port = (int) ($this->settings['email']['port'] ?? 587);
                $settings->username = (string) ($this->settings['email']['username'] ?? '');
                $settings->password = (string) ($this->settings['email']['password'] ?? '');
                $settings->encryption = (string) ($this->settings['email']['encryption'] ?? '');
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

    /**
     * Send a test email with the current (possibly unsaved) email form values so
     * the operator can verify delivery before saving. Synchronous so SMTP errors
     * surface immediately in the toast.
     */
    public function sendTestMail(): void
    {
        $this->authorize('manage.settings');

        $this->validate([
            'testRecipient' => ['required', 'email'],
            'settings.email.from_address' => ['required', 'email'],
        ], [], [
            'testRecipient' => 'recipient',
            'settings.email.from_address' => 'from address',
        ]);

        $email = $this->settings['email'];

        // Apply the on-screen values to runtime config so the test reflects the
        // form, not only what is already saved.
        config([
            'mail.default' => $email['mailer'] ?? config('mail.default'),
            'mail.mailers.smtp.host' => $email['host'] ?? null,
            'mail.mailers.smtp.port' => $email['port'] ?? null,
            'mail.mailers.smtp.username' => ($email['username'] ?? '') !== '' ? $email['username'] : null,
            'mail.mailers.smtp.password' => ($email['password'] ?? '') !== '' ? $email['password'] : null,
            'mail.mailers.smtp.encryption' => ($email['encryption'] ?? '') !== '' ? $email['encryption'] : null,
            'mail.from.address' => $email['from_address'],
            'mail.from.name' => $email['from_name'] ?? config('app.name'),
        ]);

        $appName = config('app.name');

        try {
            Mail::raw(
                "This is a test email from {$appName}. If you received it, your mail configuration is working.",
                fn ($message) => $message->to($this->testRecipient)->subject("Test email from {$appName}"),
            );

            $this->dispatch('toast', type: 'success', message: __('Test email sent to :recipient.', ['recipient' => $this->testRecipient]), duration: 5000);
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: __('Failed to send test email: :error', ['error' => $e->getMessage()]), duration: 8000);
        }
    }

    public function render(): View
    {
        return view('livewire.admin.settings.show');
    }
}
