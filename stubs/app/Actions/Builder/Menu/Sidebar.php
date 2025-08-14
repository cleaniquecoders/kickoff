<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Sidebar implements Builder, Menu
{
    private Collection $menus;

    /**
     * Return the list of menus.
     */
    public function menus(): Collection
    {
        return $this->menus;
    }

    /**
     * Build the administration menu items.
     */
    public function build(): self
    {
        $this->menus = collect([

            (new MenuItem)
                ->setLabel(__('Dashboard'))
                ->setUrl(route('dashboard'))
                ->setVisible(fn () => Auth::check())
                ->setTooltip(__('Dashboard'))
                ->setIcon('layout-dashboard')
                ->setDescription(__('Access to your dashboard.')),

        ])
            ->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
