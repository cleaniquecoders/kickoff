<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class SidebarFooter extends Base
{
    /**
     * Build the sidebar footer menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Quick Actions'))
            ->setHeadingIcon('zap');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($this->filterEmptyMenuItems($menuItems));

        return $this;
    }

    /**
     * Get menu configuration for sidebar footer.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createDocumentationMenuItem(),
            fn () => $this->createSupportMenuItem(),
            fn () => $this->createChangelogMenuItem(),
            // fn () => $this->createLogoutMenuItem(),
        ];
    }

    /**
     * Create the documentation menu item.
     */
    private function createDocumentationMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Documentation'))
            ->setUrl('#')
            ->setTarget('_blank')
            ->setIcon('book-open')
            ->setDescription(__('View Documentation'))
            ->setVisible(fn () => Gate::allows('access.dashboard'));
    }

    /**
     * Create the support menu item.
     */
    private function createSupportMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Support'))
            ->setUrl('#')
            ->setTarget('_blank')
            ->setIcon('life-buoy')
            ->setDescription(__('Get help and support'))
            ->setVisible(fn () => Gate::allows('access.dashboard'));
    }

    /**
     * Create the changelog menu item.
     */
    private function createChangelogMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Changelog'))
            ->setUrl('#')
            ->setTarget('_blank')
            ->setIcon('newspaper')
            ->setDescription(__('View latest updates'))
            ->setVisible(fn () => Gate::allows('access.dashboard'));
    }

    /**
     * Create the logout menu item.
     */
    private function createLogoutMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Logout'))
            ->setUrl(route('logout'))
            ->setType('form')
            ->setIcon('log-out')
            ->setDescription(__('Sign out of your account'))
            ->setVisible(fn () => Gate::allows('access.dashboard'));
    }
}
