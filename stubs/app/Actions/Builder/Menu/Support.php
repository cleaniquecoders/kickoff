<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;

class Support extends Base
{
    /**
     * Build the support menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Support & Monitoring'))
            ->setHeadingIcon('life-buoy')
            ->setAuthorization('access.admin-panel');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for support.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createTelescopeMenuItem(),
            fn () => $this->createHorizonMenuItem(),
        ];
    }

    /**
     * Create the Telescope menu item.
     */
    private function createTelescopeMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Telescope'))
            ->setUrl(route('telescope'))
            ->setIcon('bug')
            ->setDescription(__('Access application debugging using Laravel Telescope'))
            ->setTooltip(__('Telescope'))
            ->setTarget('_blank')
            ->setVisible(fn () => \Illuminate\Support\Facades\Gate::allows('access.telescope'));
    }

    /**
     * Create the Horizon menu item.
     */
    private function createHorizonMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Horizon'))
            ->setUrl(route('horizon.index'))
            ->setIcon('arrow-right-left')
            ->setDescription(__('Access Laravel Horizon to monitor and manage queues'))
            ->setTooltip(__('Horizon'))
            ->setTarget('_blank')
            ->setVisible(fn () => \Illuminate\Support\Facades\Gate::allows('access.horizon'));
    }
}
