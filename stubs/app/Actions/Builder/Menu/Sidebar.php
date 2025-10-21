<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class Sidebar extends Base
{
    /**
     * Build the sidebar menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Navigation'))
            ->setHeadingIcon('menu')
            ->setAuthorization('access.dashboard');

        $this->menus = collect([

            (new MenuItem)
                ->setLabel(__('Dashboard'))
                ->setUrl(route('dashboard'))
                ->setVisible(fn () => Gate::allows('access.dashboard'))
                ->setTooltip(__('Dashboard'))
                ->setIcon('layout-dashboard')
                ->setDescription(__('Access to your dashboard.')),

        ])
            ->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
