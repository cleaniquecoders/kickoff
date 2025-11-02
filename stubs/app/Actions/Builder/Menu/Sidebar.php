<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Base
{
    /**
     * Build the sidebar menu items.
     */
    public function build(): self
    {
        $this->setAuthorization(fn () => Auth::check());

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for the sidebar.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createDashboardMenuItem(),
        ];
    }

    /**
     * Create the dashboard menu item.
     */
    private function createDashboardMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Dashboard'))
            ->setUrl(route('dashboard'))
            ->setIcon('gauge')
            ->setDescription(__('Access to your dashboard.'))
            ->setTooltip(__('Dashboard'))
            ->setVisible(true);
    }
}
