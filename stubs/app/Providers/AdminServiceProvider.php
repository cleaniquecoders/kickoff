<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        Gate::define('viewAdmin', function ($user) {
            return $user->can('manage-administration');
        });

        Gate::define('viewUser', function ($user) {
            return $user->can('manage-user');
        });
        Gate::define('viewAudit', function ($user) {
            return $user->can('view-audit');
        });
        Gate::define('viewAccessControl', function ($user) {
            return config('access-control.enabled') && $user->can('manage-access-control');
        });
    }

    private function checkGates()
    {
        Gate::check('viewAdmin', [request()->user()]);

        Gate::check('viewUser', [request()->user()]);

        Gate::check('viewAudit', [request()->user()]);

        Gate::check('viewAccessControl', [request()->user()]);
    }
}
