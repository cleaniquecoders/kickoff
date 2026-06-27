<?php

declare(strict_types=1);

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/**
 * Administration menu — a single g8stack-style group that nests the common
 * admin areas as sub-groups: Identity, Mail, Backups, Settings, Developers.
 *
 * Every leaf is route-guarded (Route::has(...) ? route(...) : '#') and gated, so
 * an item only appears when its feature/package is installed in the host app —
 * making this one builder safe across projects with different feature sets.
 *
 * Icons are Lucide names; any not already published are imported via
 * `php artisan flux:icon ...` (see the scaffolder / docs).
 */
class Administration extends Base
{
    /**
     * Build the administration menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Administration'))
            ->setHeadingIcon('settings')
            ->setHeadingUrl(Route::has('admin.index') ? route('admin.index') : null)
            ->setAuthorization(fn () => Gate::any([
                'access.user-management',
                'access.media-management',
                'access.settings',
                'access.audit-monitoring',
            ]));

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for administration.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createIdentityMenuGroup(),
            fn () => $this->createMailMenuGroup(),
            fn () => $this->createBackupsMenuGroup(),
            fn () => $this->createSettingsMenuGroup(),
            fn () => $this->createDeveloperMenuGroup(),
        ];
    }

    /**
     * A route-guarded child: hidden unless one of the candidate routes exists and
     * its paired ability passes.
     *
     * Pass a single route + ability for the common case, or parallel arrays of
     * candidates to stay portable across projects that expose the same feature
     * under a different route name / permission — e.g. MCP is `mcp-kit.tasks`
     * (gated by `mcp-kit.view-tasks`) in a fresh Kickoff app but
     * `ops.settings.mcp-tokens` (gated by `ops.access.dashboard`) elsewhere. The
     * first existing route wins for the URL; visibility passes if any existing
     * route's paired ability is granted. When fewer abilities than routes are
     * given, the last ability is reused for the remaining routes.
     *
     * Pass $routeParams for routes that take URL parameters (e.g. the
     * /admin/settings/{section} sections), applied to whichever candidate route
     * resolves.
     *
     * @param  string|array<int, string>  $route
     * @param  string|array<int, string>  $ability
     * @param  array<int|string, mixed>  $routeParams
     */
    private function child(string $label, string|array $route, string $icon, string|array $ability, string $tooltip = '', bool $newTab = false, array $routeParams = []): MenuItem
    {
        $routes = array_values((array) $route);
        $abilities = array_values((array) $ability);
        $lastAbility = end($abilities) ?: '';

        $resolved = collect($routes)->first(fn (string $r) => Route::has($r));

        $item = (new MenuItem)
            ->setLabel(__($label))
            ->setUrl($resolved !== null ? route($resolved, $routeParams) : '#')
            ->setIcon($icon)
            ->setTooltip($tooltip !== '' ? __($tooltip) : __($label))
            ->setVisible(function () use ($routes, $abilities, $lastAbility) {
                foreach ($routes as $i => $r) {
                    if (Route::has($r) && Gate::allows($abilities[$i] ?? $lastAbility)) {
                        return true;
                    }
                }

                return false;
            });

        return $newTab ? $item->setTarget('_blank') : $item;
    }

    /**
     * Identity — users & roles.
     */
    private function createIdentityMenuGroup(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Identity'))
            ->setUrl('#')
            ->setIcon('users-round')
            ->setTooltip(__('Users and roles'))
            ->setDescription(__('Manage user accounts and roles'))
            ->setVisible(fn () => (Route::has('security.users.index') && Gate::allows('manage.users'))
                || (Route::has('admin.roles.index') && Gate::allows('manage.roles')))
            ->addChild($this->child('Users', 'security.users.index', 'users', 'manage.users', 'Manage users'))
            ->addChild($this->child('Roles', 'admin.roles.index', 'shield-check', 'manage.roles', 'Manage roles'));
    }

    /**
     * Mail — settings + the outbound mail history log.
     *
     * History maps to the APP-OWNED admin.mail-history.index route (the
     * cleaniquecoders/mailhistory package only ships tracking-pixel routes, not an
     * index page), with `mailhistory.index` kept as a fallback name. It stays
     * hidden until that route + the admin.view.mail-history gate exist.
     */
    private function createMailMenuGroup(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Mail'))
            ->setUrl('#')
            ->setIcon('mail')
            ->setTooltip(__('Email settings and history'))
            ->setDescription(__('Mail configuration and the outbound email log'))
            ->setVisible(fn () => (Route::has('admin.settings.index') && Gate::allows('manage.settings'))
                || (Route::has('admin.mail-history.index') && Gate::allows('admin.view.mail-history'))
                || (Route::has('mailhistory.index') && Gate::allows('admin.view.mail-history')))
            ->addChild($this->child('Settings', 'admin.settings.show', 'cog', 'manage.settings', 'Mail / SMTP configuration', false, ['section' => 'email']))
            ->addChild($this->child('History', ['admin.mail-history.index', 'mailhistory.index'], 'history', 'admin.view.mail-history', 'Outbound email audit log'));
    }

    /**
     * Backups — application & configuration backups.
     */
    private function createBackupsMenuGroup(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Backups'))
            ->setUrl('#')
            ->setIcon('hard-drive')
            ->setTooltip(__('Backup and restore'))
            ->setDescription(__('Application and configuration backups'))
            ->setVisible(fn () => (Route::has('admin.settings.backups') && Gate::allows('admin.manage.backups'))
                || (Route::has('config-backup.index') && Gate::allows('admin.manage.config-backup')))
            ->addChild($this->child('App Backups', 'admin.settings.backups', 'save', 'admin.manage.backups', 'Manage application backups'))
            ->addChild($this->child('Config Backup', 'config-backup.index', 'archive', 'admin.manage.config-backup', 'Backup and restore configuration'));
    }

    /**
     * Settings — general, notifications, SSO, webhooks.
     */
    private function createSettingsMenuGroup(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Settings'))
            ->setUrl('#')
            ->setIcon('sliders-horizontal')
            ->setTooltip(__('System settings'))
            ->setDescription(__('General settings, notifications, single sign-on, and webhooks'))
            ->setVisible(fn () => (Route::has('admin.settings.index') && Gate::allows('manage.settings'))
                || (Route::has('config-sso.admin') && Gate::allows('admin.manage.sso'))
                || (Route::has('config-webhook.index') && Gate::allows('admin.manage.webhooks')))
            ->addChild($this->child('General', 'admin.settings.show', 'sliders-horizontal', 'manage.settings', 'Configure system-wide settings', false, ['section' => 'general']))
            ->addChild($this->child('Authentication', 'admin.settings.authentication', 'lock-keyhole', 'manage.settings', 'Control sign-in and public registration'))
            ->addChild($this->child('Notifications', 'admin.settings.show', 'bell', 'manage.settings', 'Enable notifications and choose delivery channels', false, ['section' => 'notifications']))
            ->addChild($this->child('SSO', 'config-sso.admin', 'key-round', 'admin.manage.sso', 'Configure single sign-on providers'))
            ->addChild($this->child('Webhooks', 'config-webhook.index', 'webhook', 'admin.manage.webhooks', 'Manage outgoing webhooks'));
    }

    /**
     * Developers — audit trail, MCP, artisan runner, telescope, horizon.
     */
    private function createDeveloperMenuGroup(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Developers'))
            ->setUrl('#')
            ->setIcon('code')
            ->setTooltip(__('Developer tools'))
            ->setDescription(__('Audit trail and operational/debugging tools'))
            ->setVisible(fn () => (Route::has('security.audit-trail.index') && Gate::allows('view.audit-logs'))
                || (Route::has('settings.mcp-tokens.show') && Gate::allows('mcp-kit.view-tasks'))
                || (Route::has('artisan-runner.index') && Gate::allows('access.artisan-runner'))
                || (Route::has('telescope') && Gate::allows('access.telescope'))
                || (Route::has('horizon.index') && Gate::allows('access.horizon')))
            ->addChild($this->child('Audit Trail', 'security.audit-trail.index', 'list-checks', 'view.audit-logs', 'Audit logs for security and activity'))
            ->addChild($this->child('MCP Tokens', ['settings.mcp-tokens.show', 'ops.settings.mcp-tokens', 'mcp-tokens'], 'cpu', ['mcp-kit.view-tasks', 'ops.access.dashboard', 'access.mcp'], 'Connect AI clients to the MCP server'))
            ->addChild($this->child('Artisan Runner', 'artisan-runner.index', 'terminal', 'access.artisan-runner', 'Run allowlisted Artisan commands', true))
            ->addChild($this->child('Telescope', 'telescope', 'bug', 'access.telescope', 'Application debugging via Telescope', true))
            ->addChild($this->child('Horizon', 'horizon.index', 'layers', 'access.horizon', 'Monitor and manage queues', true));
    }
}
