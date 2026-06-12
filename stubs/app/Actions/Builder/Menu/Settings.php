<?php

declare(strict_types=1);

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class Settings extends Base
{
    /**
     * Build the settings menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Settings'))
            ->setHeadingIcon('cog-6-tooth')
            ->setAuthorization(fn () => Gate::allows('access.settings'));

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for settings.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createGeneralSettingsMenuItem(),
            fn () => $this->createWebhooksMenuItem(),
            fn () => $this->createConfigBackupMenuItem(),
            fn () => $this->createSsoMenuItem(),
        ];
    }

    /**
     * Create the general settings menu item.
     */
    private function createGeneralSettingsMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('General'))
            ->setUrl(route('admin.settings.index'))
            ->setVisible(fn () => Gate::allows('manage.settings'))
            ->setTooltip(__('General settings'))
            ->setDescription(__('Configure system-wide settings'))
            ->setIcon('adjustments-horizontal');
    }

    /**
     * Create the webhooks menu item.
     */
    private function createWebhooksMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Webhooks'))
            ->setUrl(Route::has('config-webhook.index') ? route('config-webhook.index') : '#')
            ->setVisible(fn () => Route::has('config-webhook.index') && Gate::allows('admin.manage.webhooks'))
            ->setTooltip(__('Manage webhooks'))
            ->setDescription(__('Manage outgoing webhook subscriptions and delivery logs'))
            ->setIcon('webhook');
    }

    /**
     * Create the configuration backup menu item.
     */
    private function createConfigBackupMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Config Backup'))
            ->setUrl(Route::has('config-backup.index') ? route('config-backup.index') : '#')
            ->setVisible(fn () => Route::has('config-backup.index') && Gate::allows('admin.manage.config-backup'))
            ->setTooltip(__('Configuration backups'))
            ->setDescription(__('Backup and restore application configuration'))
            ->setIcon('archive-box');
    }

    /**
     * Create the SSO providers menu item.
     */
    private function createSsoMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('SSO Providers'))
            ->setUrl(Route::has('config-sso.admin') ? route('config-sso.admin') : '#')
            ->setVisible(fn () => Route::has('config-sso.admin') && Gate::allows('admin.manage.sso'))
            ->setTooltip(__('SSO providers'))
            ->setDescription(__('Configure single sign-on providers'))
            ->setIcon('key');
    }
}
