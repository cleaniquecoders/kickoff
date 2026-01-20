<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
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
        $this->defineMainAccessGates();
        $this->defineUserManagementGates();
        $this->defineRoleManagementGates();
        $this->defineAdministrationGates();
        $this->defineSecurityGates();
        $this->defineMonitoringGates();
        $this->defineMediaManagementGates();
        $this->defineProfileGates();
        $this->defineCompositeGates();
    }

    /**
     * Define main access gates for sidebar/menu authorization.
     */
    private function defineMainAccessGates(): void
    {
        Gate::define('access.admin-panel', function (User $user) {
            return $user->can('admin.view.panel');
        });

        Gate::define('access.dashboard', function (User $user) {
            return $user->can('dashboard.access.user') || $user->can('dashboard.access.admin');
        });
    }

    /**
     * Define user management gates.
     */
    private function defineUserManagementGates(): void
    {
        Gate::define('manage.users', function (User $user) {
            return $user->can('users.view.list');
        });

        Gate::define('impersonate.users', function (User $user) {
            return $user->can('admin.impersonate.users');
        });
    }

    /**
     * Define role management gates.
     */
    private function defineRoleManagementGates(): void
    {
        Gate::define('manage.roles', function (User $user) {
            return $user->can('roles.view.list');
        });
    }

    /**
     * Define user management menu gates.
     * Combines users and roles management.
     */
    private function defineAdministrationGates(): void
    {
        // User Management menu access gate (combines users + roles)
        Gate::define('access.user-management', function (User $user) {
            return $user->can('manage.users') || $user->can('manage.roles');
        });

        // Settings menu access gate
        Gate::define('access.settings', function (User $user) {
            return $user->can('manage.settings');
        });

        // Settings management
        Gate::define('manage.settings', function (User $user) {
            return $user->can('admin.manage.settings');
        });
    }

    /**
     * Define security-related gates.
     */
    private function defineSecurityGates(): void
    {
        Gate::define('view.audit-logs', function (User $user) {
            return $user->can('security.view.audit-logs');
        });
    }

    /**
     * Define monitoring and tools gates.
     */
    private function defineMonitoringGates(): void
    {
        // Audit & Monitoring menu access gate
        Gate::define('access.audit-monitoring', function (User $user) {
            return $user->can('view.audit-logs') || $user->can('access.telescope') || $user->can('access.horizon');
        });

        Gate::define('access.telescope', function (User $user) {
            return $user->can('admin.access.telescope') && App::environment(['local', 'staging']);
        });

        Gate::define('access.horizon', function (User $user) {
            return $user->can('admin.access.horizon');
        });

        // Gates required by Laravel packages (Telescope and Horizon)
        Gate::define('viewTelescope', function (User $user) {
            return $user->can('access.telescope');
        });

        Gate::define('viewHorizon', function (User $user) {
            return $user->can('access.horizon');
        });
    }

    /**
     * Define media management gates.
     */
    private function defineMediaManagementGates(): void
    {
        Gate::define('access.media-management', function (User $user) {
            return $user->can('manage.media');
        });

        Gate::define('manage.media', function (User $user) {
            return $user->can('media.access.management');
        });
    }

    /**
     * Define profile and user self-service gates.
     */
    private function defineProfileGates(): void
    {
        Gate::define('access.profile', function (User $user) {
            return $user->can('profile.view.own');
        });

        Gate::define('access.notifications', function (User $user) {
            return $user->can('notifications.view.own');
        });
    }

    /**
     * Define composite gates for different access levels.
     */
    private function defineCompositeGates(): void
    {
        Gate::define('access.superadmin', function (User $user) {
            return $user->can('admin.view.panel') && $user->can('admin.manage.settings');
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
            Gate::check('access.user-management', [$user]);
            Gate::check('access.media-management', [$user]);
            Gate::check('access.settings', [$user]);
            Gate::check('access.audit-monitoring', [$user]);
            Gate::check('manage.settings', [$user]);
        }

        // Check user self-service gates
        Gate::check('access.profile', [$user]);
        Gate::check('access.notifications', [$user]);
    }
}
