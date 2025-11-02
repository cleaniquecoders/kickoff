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
            // fn () => $this->createAccessControlMenuItem(),
            // fn () => $this->createAuditTrailMenuItem(),
        ];
    }

    /**
     * Create the access control menu item.
     */
    private function createAccessControlMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Access Control'))
            ->setUrl(route('security.access-control.index'))
            ->setVisible(fn () => Gate::allows('manage.access-control'))
            ->setTooltip(__('Manage access control'))
            ->setDescription(__('Define and manage access control rules'))
            ->setIcon('lock');
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
            ->setIcon('scroll-text');
    }
}
