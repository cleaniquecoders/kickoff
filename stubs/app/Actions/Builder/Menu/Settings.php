<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

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
}
