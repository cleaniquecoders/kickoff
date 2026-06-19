<?php

declare(strict_types=1);

namespace App\Actions\Builder;

use App\Actions\Builder\Menu\Administration;
use App\Actions\Builder\Menu\MediaManagement;
use App\Actions\Builder\Menu\Sidebar;
use App\Actions\Builder\Menu\SidebarFooter;
use App\Exceptions\ContractException;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu as ContractsMenu;

class Menu
{
    public static function make(): self
    {
        return new self;
    }

    public function build(string $builder): Builder|ContractsMenu
    {
        $class = match ($builder) {
            'sidebar' => Sidebar::class,
            'media-management' => MediaManagement::class,
            'administration' => Administration::class,
            'sidebar-footer' => SidebarFooter::class,
            default => Sidebar::class,
        };

        $instance = new $class;

        ContractException::throwUnless(! $instance instanceof Builder, 'missingContract', $class, Builder::class);
        ContractException::throwUnless(! $instance instanceof ContractsMenu, 'missingContract', $class, ContractsMenu::class);

        return $instance->build();
    }
}
