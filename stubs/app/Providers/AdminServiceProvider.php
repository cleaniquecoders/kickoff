<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->authorize();
    }

    protected function authorize()
    {
        $this->defineGates();
        $this->checkGates();
    }

    private function defineGates()
    {
        // Main access gates - for sidebar/menu authorization
        Gate::define('access.admin-panel', function (User $user) {
            return $user->can('admin.view.panel');
        });

        Gate::define('access.dashboard', function (User $user) {
            return $user->can('dashboard.access.user') || $user->can('dashboard.access.admin');
        });

        // User management gates
        Gate::define('manage.users', function (User $user) {
            return $user->can('users.view.list');
        });

        // Role management gates
        Gate::define('manage.roles', function (User $user) {
            return $user->can('roles.view.list');
        });

        // Security gates
        Gate::define('access.security', function (User $user) {
            return $user->can('security.manage.access-control') || $user->can('security.view.audit-logs');
        });

        Gate::define('manage.access-control', function (User $user) {
            return Config::get('access-control.enabled') && $user->can('security.manage.access-control');
        });

        Gate::define('view.audit-logs', function (User $user) {
            return $user->can('security.view.audit-logs');
        });

        // Monitoring and tools gates
        Gate::define('access.telescope', function (User $user) {
            return $user->can('admin.access.telescope') && App::environment(['local', 'staging']);
        });

        Gate::define('access.horizon', function (User $user) {
            return $user->can('admin.access.horizon');
        });

        // Settings gates
        Gate::define('manage.settings', function (User $user) {
            return $user->can('admin.manage.settings');
        });

        // Impersonation gates
        Gate::define('impersonate.users', function (User $user) {
            return $user->can('admin.impersonate.users');
        });

        // Profile gates - for user self-service
        Gate::define('access.profile', function (User $user) {
            return $user->can('profile.view.own');
        });

        Gate::define('access.notifications', function (User $user) {
            return $user->can('notifications.view.own');
        });

        // Composite gates for different access levels
        Gate::define('access.superadmin', function (User $user) {
            return $user->can('admin.view.panel') && $user->can('admin.manage.settings');
        });

        // Legacy gates for backward compatibility
        Gate::define('viewUser', function (User $user) {
            return $user->can('manage.users');
        });

        Gate::define('viewAudit', function (User $user) {
            return $user->can('view.audit-logs');
        });

        Gate::define('viewAccessControl', function (User $user) {
            return $user->can('manage.access-control');
        });

        Gate::define('viewTelescope', function (User $user) {
            return $user->can('access.telescope');
        });

        Gate::define('viewHorizon', function (User $user) {
            return $user->can('access.horizon');
        });

        Gate::define('admin-access', function (User $user) {
            return $user->can('access.admin-panel');
        });

        Gate::define('superadmin-access', function (User $user) {
            return $user->can('access.superadmin');
        });
    }

    private function checkGates()
    {
        if (! Request::user()) {
            return;
        }

        $user = Request::user();

        // Check main access gates
        Gate::check('access.dashboard', [$user]);
        Gate::check('access.admin-panel', [$user]);

        // Check specific gates if user has admin access
        if (Gate::allows('access.admin-panel', [$user])) {
            Gate::check('manage.users', [$user]);
            Gate::check('manage.roles', [$user]);
            Gate::check('access.security', [$user]);
            Gate::check('manage.settings', [$user]);
        }

        // Check user self-service gates
        Gate::check('access.profile', [$user]);
        Gate::check('access.notifications', [$user]);
    }
}
