<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class SidebarFooter extends Base
{
    public function build(): self
    {
        $this->setHeadingLabel(__('Quick Actions'))
            ->setHeadingIcon('zap');
        $menuItems = [
            (new MenuItem)
                ->setLabel(__('Documentation'))
                ->setUrl('#')
                ->setTarget('_blank')
                ->setIcon('book-open')
                ->setDescription(__('View Documentation'))
                ->setVisible(fn () => Gate::allows('access.dashboard')),
            (new MenuItem)
                ->setLabel(__('Support'))
                ->setUrl('#')
                ->setTarget('_blank')
                ->setIcon('life-buoy')
                ->setDescription(__('Get help and support'))
                ->setVisible(fn () => Gate::allows('access.dashboard')),
            (new MenuItem)
                ->setLabel(__('Changelog'))
                ->setUrl('#')
                ->setTarget('_blank')
                ->setIcon('newspaper')
                ->setDescription(__('View latest updates'))
                ->setVisible(fn () => Gate::allows('access.dashboard')),
            (new MenuItem)
                ->setLabel(__('Logout'))
                ->setUrl(route('logout'))
                ->setType('form')
                ->setIcon('log-out')
                ->setDescription(__('Sign out of your account'))
                ->setVisible(fn () => Gate::allows('access.dashboard')),
        ];

        // Build all menu items and convert to collection
        $this->menus = collect($menuItems)
            ->map(fn (MenuItem $item) => $item->build()->toArray())
            ->filter(fn (array $item) => $item !== []);

        return $this;
    }
}
