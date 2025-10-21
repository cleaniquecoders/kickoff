<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class Administration extends Base
{
    /**
     * Build the administration menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Administration'))
            ->setHeadingIcon('settings')
            ->setAuthorization('access.superadmin');

        $this->menus = collect([
            (new MenuItem)
                ->setLabel(__('Roles'))
                ->setUrl(route('admin.roles.index'))
                ->setVisible(fn () => Gate::allows('manage.roles'))
                ->setTooltip(__('Manage roles'))
                ->setDescription(__('Define and manage user roles'))
                ->setIcon('shield-check'),

            (new MenuItem)
                ->setLabel(__('Settings'))
                ->setUrl(route('admin.settings.index'))
                ->setVisible(fn () => Gate::allows('manage.settings'))
                ->setTooltip(__('System settings'))
                ->setDescription(__('Configure system-wide settings'))
                ->setIcon('cog'),
        ])->reject(fn (MenuItem $menu) => ! $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray());

        return $this;
    }
}
