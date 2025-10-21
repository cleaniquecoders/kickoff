<?php

use App\Actions\Builder\Menu as Action;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu;

if (! function_exists('menu')) {
    /**
     * Menu helper to build menus based on type.
     *
     * @param  string  $builder  See app/Actions/Builder/Menu.php for the available menue.
     * @return \CleaniqueCoders\Traitify\Contracts\Builder
     */
    function menu(string $builder): Builder|Menu
    {
        return Action::make()->build($builder);
    }
}
