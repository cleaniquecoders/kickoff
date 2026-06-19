<?php

declare(strict_types=1);

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Sidebar footer — the "Resources" group (Documentation, Support, Changelog),
 * pinned to the bottom of the sidebar. Items are route-guarded so they only
 * appear where the route exists in the host app.
 */
class SidebarFooter extends Base
{
    /**
     * Build the sidebar footer menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Resources'))
            ->setHeadingIcon('zap')
            ->setAuthorization(fn () => Auth::check());

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
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

    private function createDocumentationMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Documentation'))
            ->setUrl(Route::has('documentation') ? route('documentation') : '#')
            ->setIcon('book-open')
            ->setTooltip(__('View documentation'))
            ->setVisible(fn () => Route::has('documentation'));
    }

    private function createSupportMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Support'))
            ->setUrl(Route::has('support') ? route('support') : '#')
            ->setIcon('life-buoy')
            ->setTooltip(__('Get help and support'))
            ->setVisible(fn () => Route::has('support'));
    }

    private function createChangelogMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Changelog'))
            ->setUrl(Route::has('changelog') ? route('changelog') : '#')
            ->setIcon('newspaper')
            ->setTooltip(__('View latest updates'))
            ->setVisible(fn () => Route::has('changelog'));
    }
}
