<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\AssignDefaultRole;
use App\Listeners\LogoutFromOtherDevices;
use App\Listeners\RevokeClaimedAccountCredentials;
use CleaniqueCoders\ConfigSso\Events\UnverifiedAccountClaimed;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * Note: Email verification listener is automatically registered by Laravel's
     * base EventServiceProvider via configureEmailVerification(). Do not add it
     * here to avoid duplicate emails. Event discovery is disabled in
     * bootstrap/app.php (withEvents(discover: false)), so this map is the
     * only place a listener is registered.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Authenticated::class => [
            LogoutFromOtherDevices::class,
        ],
        Registered::class => [
            AssignDefaultRole::class,
        ],
        UnverifiedAccountClaimed::class => [
            RevokeClaimedAccountCredentials::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {}

    /**
     * Configure the proper event listeners for email verification.
     *
     * This is intentionally empty because Laravel 11+ automatically registers
     * the Foundation EventServiceProvider via Application::configure()->withEvents().
     * That provider already handles email verification listener registration.
     * Overriding this prevents duplicate email verification notifications.
     */
    protected function configureEmailVerification(): void
    {
        // Intentionally empty - handled by Foundation EventServiceProvider
    }
}
