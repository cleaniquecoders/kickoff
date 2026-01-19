<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class Security extends Base
{
    /**
     * Build the security menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Security'))
            ->setHeadingIcon('shield')
            ->setAuthorization('access.security');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for security.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createUsersMenuItem(),
            fn () => $this->createAuditTrailMenuItem(),
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
            ->setIcon('users');
    }

    /**
     * Create the audit trail menu item.
     */
    private function createAuditTrailMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Audit Trail'))
            ->setUrl(route('security.audit-trail.index'))
            ->setVisible(fn () => Gate::allows('view.audit-logs'))
            ->setTooltip(__('View audit trails'))
            ->setDescription(__('Audit logs for security and activity tracking'))
            ->setIcon('clipboard-document-list');
    }
}
