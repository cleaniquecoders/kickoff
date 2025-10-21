<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class Event extends Base
{
    /**
     * Build the event menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Application Features'))
            ->setHeadingIcon('puzzle-piece')
            ->setAuthorization('access.admin-panel');

        $this->menus = collect([
            // Add your application-specific menu items here
            // Example:
            // (new MenuItem)
            //     ->setLabel(__('Custom Module'))
            //     ->setUrl(route('custom.index'))
            //     ->setVisible(fn () => Gate::allows('manage.custom-module'))
            //     ->setTooltip(__('Manage custom module'))
            //     ->setDescription(__('Access custom application features'))
            //     ->setIcon('cog'),
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
