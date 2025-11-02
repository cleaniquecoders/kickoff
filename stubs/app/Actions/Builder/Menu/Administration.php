<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class Administration extends Base
{
    /**
     * Build the administration menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Administration'))
            ->setHeadingIcon('settings')
            ->setAuthorization(true);

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
            // fn () => $this->createRolesMenuItem(),
            fn () => $this->createSettingsMenuItem(),
        ];
    }

    /**
     * Create the roles menu item.
     */
    private function createRolesMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Roles'))
            ->setUrl(route('admin.roles.index'))
            ->setVisible(fn () => Gate::allows('manage.roles'))
            ->setTooltip(__('Manage roles'))
            ->setDescription(__('Define and manage user roles'))
            ->setIcon('shield-check');
    }

    /**
     * Create the settings menu item.
     */
    private function createSettingsMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Settings'))
            ->setUrl(route('admin.settings.index'))
            ->setVisible(fn () => true)
            ->setTooltip(__('Manage roles'))
            ->setDescription(__('Configure system-wide settings'))
            ->setIcon('cog');
    }
}
