<?php

declare(strict_types=1);

namespace App\Providers;

use App\Settings\AuthenticationSettings;
use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\NotificationSettings;
use App\Settings\SeoSettings;
use CleaniqueCoders\ArtisanRunner\Livewire\CommandRunner;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;
use Livewire\Livewire;

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

        Password::defaults(function () {
            $config = config('security.password');

            $rule = Password::min($config['min_length'] ?? 12);

            if ($config['require_mixed_case'] ?? true) {
                $rule->mixedCase();
            }
            if ($config['require_numbers'] ?? true) {
                $rule->numbers();
            }
            if ($config['require_symbols'] ?? true) {
                $rule->symbols();
            }
            if ($config['require_uncompromised'] ?? true) {
                $rule->uncompromised();
            }

            return $rule;
        });

        $this->applyDatabaseSettings();
        $this->registerVendorLivewireNamespaces();
    }

    /**
     * Workaround for cleaniquecoders/laravel-artisan-runner <= 1.2.x on Livewire 4:
     * the package checks method_exists() on the Livewire FACADE (always false for
     * __callStatic-proxied methods), so it falls back to Livewire::component()
     * with a `::` name — which Livewire 4 never resolves. Registering the
     * namespace here makes `artisan-runner::command-runner` resolvable.
     * Remove once the package fixes the facade check upstream.
     */
    private function registerVendorLivewireNamespaces(): void
    {
        if (! class_exists(CommandRunner::class)) {
            return;
        }

        Livewire::addNamespace(
            namespace: 'artisan-runner',
            classNamespace: 'CleaniqueCoders\\ArtisanRunner\\Livewire',
            classPath: base_path('vendor/cleaniquecoders/laravel-artisan-runner/src/Livewire'),
            classViewPath: base_path('vendor/cleaniquecoders/laravel-artisan-runner/resources/views/livewire'),
        );
    }

    /**
     * Override config values with Spatie Settings from the database.
     */
    private function applyDatabaseSettings(): void
    {
        try {
            $general = app(GeneralSettings::class);
            config(['app.name' => $general->site_name]);

            // Admin-editable timezone. config() alone doesn't re-apply once the
            // framework has set the default during boot, so set it explicitly too.
            if ($general->timezone !== '' && in_array($general->timezone, timezone_identifiers_list(), true)) {
                config(['app.timezone' => $general->timezone]);
                date_default_timezone_set($general->timezone);
            }

            $auth = app(AuthenticationSettings::class);
            config(['admin.public_registration' => $auth->public_registration_enabled]);

            // When public registration is disabled, drop Fortify's registration
            // feature so its `register` / `register.store` routes are never
            // registered (this provider boots before the Fortify package
            // provider). The login view hides the "Sign up" link via
            // Route::has('register'), and direct POSTs 404.
            if (! $auth->public_registration_enabled) {
                config([
                    'fortify.features' => array_values(array_filter(
                        (array) config('fortify.features', []),
                        fn ($feature) => $feature !== Features::registration(),
                    )),
                ]);
            }

            $mail = app(MailSettings::class);
            config([
                'mail.default' => $mail->mailer,
                'mail.mailers.smtp.host' => $mail->host,
                'mail.mailers.smtp.port' => $mail->port,
                'mail.mailers.smtp.username' => $mail->username !== '' ? $mail->username : null,
                'mail.mailers.smtp.password' => $mail->password !== '' ? $mail->password : null,
                'mail.mailers.smtp.encryption' => $mail->encryption !== '' ? $mail->encryption : null,
                'mail.from.address' => $mail->from_address,
                'mail.from.name' => $mail->from_name,
            ]);

            $notification = app(NotificationSettings::class);
            config([
                'notification.enabled' => $notification->enabled,
                'notification.default' => $notification->channels,
            ]);

            $seo = app(SeoSettings::class);
            config([
                'seo.meta.title' => $seo->meta_title,
                'seo.meta.description' => $seo->meta_description,
                'seo.meta.keywords' => $seo->meta_keywords,
                'seo.canonical' => $seo->canonical_enabled,
                'seo.og_image' => $seo->og_image,
                'seo.twitter_site' => $seo->twitter_site,
                'seo.google.analytics_id' => $seo->google_analytics_id,
                'seo.google.tag_manager_id' => $seo->google_tag_manager_id,
                'seo.google.site_verification' => $seo->google_site_verification,
                'seo.robots_txt' => $seo->robots_txt,
                'seo.organization.name' => $seo->organization_name,
                'seo.organization.logo' => $seo->organization_logo,
                'seo.organization.same_as' => $seo->organization_same_as,
            ]);
        } catch (\Throwable) {
            // Settings table may not exist yet (fresh install, migrations pending).
            // Silently fall back to .env / config defaults.
        }
    }
}
