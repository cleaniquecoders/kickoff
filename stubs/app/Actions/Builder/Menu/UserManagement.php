<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class UserManagement extends Base
{
    /**
     * Build the user management menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('User Management'))
            ->setHeadingIcon('users')
            ->setAuthorization('access.user-management');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for user management.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createUsersMenuItem(),
            fn () => $this->createRolesMenuItem(),
        ];
    }

    /**
     * Create the users menu item.
     */
    private function createUsersMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Users'))
            ->setUrl(route('security.users.index'))
            ->setVisible(fn () => Gate::allows('manage.users'))
            ->setTooltip(__('Manage users'))
            ->setDescription(__('View and manage user accounts'))
            ->setIcon('user');
    }

    /**
     * Create the roles menu item.
     */
    private function createRolesMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Roles'))
            ->setUrl(route('admin.roles.index'))
            ->setVisible(fn () => Gate::allows('manage.roles'))
            ->setTooltip(__('Manage roles'))
            ->setDescription(__('Define and manage user roles'))
            ->setIcon('shield-check');
    }
}
