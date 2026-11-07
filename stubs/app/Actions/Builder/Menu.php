<?php

declare(strict_types=1);

namespace App\Actions\Builder;

use App\Actions\Builder\Menu\Administration;
use App\Actions\Builder\Menu\Sidebar;
use App\Actions\Builder\Menu\SidebarFooter;
use App\Exceptions\ContractException;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu as ContractsMenu;
use InvalidArgumentException;

class Menu
{
    /** @var array<string, class-string> Every builder key config/menu.php may name. */
    public const BUILDERS = [
        'sidebar' => Sidebar::class,
        'administration' => Administration::class,
        'sidebar-footer' => SidebarFooter::class,
    ];

    public static function make(): self
    {
        return new self;
    }

    public function build(string $builder): Builder|ContractsMenu
    {
        // An unknown key is a typo. Falling back to Sidebar rendered it a second time.
        $class = self::BUILDERS[$builder] ?? throw new InvalidArgumentException("Unknown menu builder [{$builder}].");

        $instance = new $class;

        ContractException::throwUnless(! $instance instanceof Builder, 'missingContract', $class, Builder::class);
        ContractException::throwUnless(! $instance instanceof ContractsMenu, 'missingContract', $class, ContractsMenu::class);

        return $instance->build();
    }
}
