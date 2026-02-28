<?php

declare(strict_types=1);

namespace App\Providers;

use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\NotificationSettings;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->applyDatabaseSettings();
    }

    /**
     * Override config values with Spatie Settings from the database.
     */
    private function applyDatabaseSettings(): void
    {
        try {
            $general = app(GeneralSettings::class);
            config(['app.name' => $general->site_name]);

            $mail = app(MailSettings::class);
            config([
                'mail.from.address' => $mail->from_address,
                'mail.from.name' => $mail->from_name,
            ]);

            $notification = app(NotificationSettings::class);
            config([
                'notification.enabled' => $notification->enabled,
                'notification.default' => $notification->channels,
            ]);
        } catch (\Throwable) {
            // Settings table may not exist yet (fresh install, migrations pending).
            // Silently fall back to .env / config defaults.
        }
    }
}
