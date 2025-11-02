<?php

namespace App\Concerns;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Collection;

/**
 * Trait ProcessesMenuItems
 *
 * Provides common functionality for processing menu items in menu builders.
 */
trait ProcessesMenuItems
{
    /**
     * Process a collection of menu items by filtering visible items and converting to arrays.
     */
    protected function processMenuItems(Collection $menuItems): Collection
    {
        return $menuItems
            ->filter(fn (MenuItem $menu) => $menu->isVisible())
            ->map(fn (MenuItem $menu) => $menu->build()->toArray())
            ->values(); // Re-index the collection
    }

    /**
     * Create and process menu items from an array configuration.
     */
    protected function createAndProcessMenuItems(array $menuConfigs): Collection
    {
        return $this->processMenuItems(
            collect($menuConfigs)->map(fn (callable $config) => $config())
        );
    }

    /**
     * Filter out empty menu items from a collection.
     */
    protected function filterEmptyMenuItems(Collection $menuItems): Collection
    {
        return $menuItems->filter(fn (array $item) => ! empty($item));
    }
}
