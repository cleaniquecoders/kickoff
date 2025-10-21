<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

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

        $this->menus = collect([
            (new MenuItem)
                ->setLabel(__('Telescope'))
                ->setUrl(route('telescope'))
                ->setTarget('_blank')
                ->setVisible(fn () => Gate::allows('access.telescope'))
                ->setTooltip(__('View Telescope'))
                ->setDescription(__('Access application debugging using Laravel Telescope'))
                ->setIcon('bug'),

            (new MenuItem)
                ->setLabel(__('Horizon'))
                ->setUrl(route('horizon.index'))
                ->setTarget('_blank')
                ->setVisible(fn () => Gate::allows('access.horizon'))
                ->setTooltip(__('Manage queues'))
                ->setDescription(__('Access Laravel Horizon to monitor and manage queues'))
                ->setIcon('arrow-right-left'),

        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
