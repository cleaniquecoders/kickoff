<?php

namespace App\Actions\Builder;

use App\Actions\Builder\Menu\AuditMonitoring;
use App\Actions\Builder\Menu\Settings;
use App\Actions\Builder\Menu\Sidebar;
use App\Actions\Builder\Menu\UserManagement;
use App\Exceptions\ContractException;
use CleaniqueCoders\Traitify\Contracts\Builder;
use CleaniqueCoders\Traitify\Contracts\Menu as ContractsMenu;

class Menu
{
    public static function make()
    {
        return new self;
    }

    public function build(string $builder): Builder|ContractsMenu
    {
        $class = match ($builder) {
            'sidebar' => Sidebar::class,
            'user-management' => UserManagement::class,
            'settings' => Settings::class,
            'audit-monitoring' => AuditMonitoring::class,
            default => Sidebar::class,
        };

        /**
         * @var \CleaniqueCoders\Traitify\Contracts\Builder|\CleaniqueCoders\Traitify\Contracts\Menu
         */
        $builder = new $class;

        ContractException::throwUnless(! $builder instanceof Builder, 'missingContract', $class, Builder::class);
        ContractException::throwUnless(! $builder instanceof ContractsMenu, 'missingContract', $class, Builder::class);

        return $builder->build();
    }
}
