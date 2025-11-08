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
        ];
    }

    /**
     * Create the documentation menu item.
     */
    private function createDocumentationMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Documentation'))
            ->setUrl(route('documentation'))
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
            ->setUrl(route('support'))
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
            ->setUrl(route('changelog'))
            ->setIcon('newspaper')
            ->setDescription(__('View latest updates'))
            ->setVisible(fn () => Gate::allows('access.dashboard'));
    }
}
