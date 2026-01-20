<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class MediaManagement extends Base
{
    /**
     * Build the media management menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Media'))
            ->setHeadingIcon('photo')
            ->setAuthorization('access.media-management');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for media management.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createMediaManagerMenuItem(),
        ];
    }

    /**
     * Create the media manager menu item.
     */
    private function createMediaManagerMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Media Library'))
            ->setUrl(route('media-manager.index'))
            ->setVisible(fn () => Gate::allows('manage.media'))
            ->setTooltip(__('Manage media files'))
            ->setDescription(__('Browse, upload and manage media files'))
            ->setIcon('photo');
    }
}
