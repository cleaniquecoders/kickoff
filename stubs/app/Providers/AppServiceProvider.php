<?php

declare(strict_types=1);

namespace App\Providers;

use App\Settings\AuthenticationSettings;
use App\Settings\BliptraceSettings;
use App\Settings\GeneralSettings;
use App\Settings\MailSettings;
use App\Settings\NotificationSettings;
use App\Settings\SeoSettings;
use CleaniqueCoders\ArtisanRunner\Livewire\CommandRunner;
use G8Scope\Laravel\G8ScopeServiceProvider;
use G8Scope\Laravel\Settings\G8ScopeSettings;
use G8Scope\Php\Client;
use G8Scope\Php\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;
use Livewire\Livewire;
use ReflectionClass;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->replaceBliptraceSdkSettings();
    }

    /**
     * g8scope/laravel ships its own settings group, migration, client binding and
     * page at /g8scope/settings (any signed-in user). Kickoff replaces all four with
     * BliptraceSettings + Admin > Settings > Error tracking, which keeps unset
     * fields on env and is gated by manage.settings. Runs after the SDK's
     * register() (package providers register first) and before any boot().
     */
    private function replaceBliptraceSdkSettings(): void
    {
        $config = $this->app->make('config');

        $config->set('settings.settings', array_values(array_filter(
            (array) $config->get('settings.settings', []),
            fn ($class) => $class !== G8ScopeSettings::class,
        )));
        // Same string the SDK registers (__DIR__.'/../database/settings' from src/).
        $sdkMigrations = dirname((string) (new ReflectionClass(G8ScopeServiceProvider::class))->getFileName()).'/../database/settings';
        $config->set('settings.migrations_paths', array_values(array_filter(
            (array) $config->get('settings.migrations_paths', []),
            fn ($path) => $path !== $sdkMigrations,
        )));

        // The SDK builds its client from its own settings class, whose property
        // defaults (endpoint, enabled = true) beat config('g8scope') even when its
        // group was never migrated. Build it from config, which apply() owns.
        $this->app->singleton(Client::class, fn ($app) => new Client(Config::fromArray([
            ...(array) $config->get('g8scope', []),
            'base_path' => $app->basePath(),
            'sdk_name' => 'g8scope-laravel',
        ])));

        // The SDK page is registered at our page's path, so the app route (loaded
        // after every provider boots) replaces it; the gate covers it regardless.
        $config->set('g8scope.ui.path', 'admin/settings/bliptrace');
        $config->set('g8scope.ui.middleware', ['web', 'auth', 'can:manage.settings']);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Outside production, fail loudly on two silent Eloquent bugs: N+1 lazy loads, and
        // mass-assigned columns that are not #[Fillable] (dropped without a word — e.g.
        // email_verified_at). Not preventAccessingMissingAttributes(): a model built by
        // create()/factories lacks DB-defaulted columns until refreshed, which only adds noise.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

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

        // Separate from applyDatabaseSettings(): it never throws, and must still
        // run (falling back to env) when an earlier settings group is missing.
        BliptraceSettings::apply();
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
                'mailhistory.tracking.open.enabled' => $mail->track_opens,
                'mailhistory.tracking.click.enabled' => $mail->track_clicks,
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
