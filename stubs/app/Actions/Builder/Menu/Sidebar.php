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
            fn () => $this->createNotificationsMenuItem(),
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

    /**
     * Create the notifications menu item.
     */
    private function createNotificationsMenuItem(): MenuItem
    {
        $unreadCount = Auth::check() ? Auth::user()->unreadNotifications()->count() : 0;
        $label = __('Notifications');

        if ($unreadCount > 0) {
            $label .= ' (' . $unreadCount . ')';
        }

        return (new MenuItem)
            ->setLabel($label)
            ->setUrl(route('notifications.index'))
            ->setIcon('bell')
            ->setDescription(__('Manage your notifications'))
            ->setTooltip(__('View all notifications'))
            ->setVisible(true);
    }
}
