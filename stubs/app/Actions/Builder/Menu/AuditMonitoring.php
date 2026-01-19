<?php

namespace App\Actions\Builder\Menu;

use App\Actions\Builder\MenuItem;
use Illuminate\Support\Facades\Gate;

class AuditMonitoring extends Base
{
    /**
     * Build the audit & monitoring menu items.
     */
    public function build(): self
    {
        $this->setHeadingLabel(__('Audit & Monitoring'))
            ->setHeadingIcon('chart-bar')
            ->setAuthorization('access.audit-monitoring');

        $menuItems = $this->createAndProcessMenuItems($this->getMenuConfiguration());
        $this->setMenus($menuItems);

        return $this;
    }

    /**
     * Get menu configuration for audit & monitoring.
     *
     * @return array<callable>
     */
    protected function getMenuConfiguration(): array
    {
        return [
            fn () => $this->createAuditTrailMenuItem(),
            fn () => $this->createTelescopeMenuItem(),
            fn () => $this->createHorizonMenuItem(),
        ];
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

    /**
     * Create the Telescope menu item.
     */
    private function createTelescopeMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Telescope'))
            ->setUrl(route('telescope'))
            ->setIcon('bug-ant')
            ->setDescription(__('Access application debugging using Laravel Telescope'))
            ->setTooltip(__('Telescope'))
            ->setTarget('_blank')
            ->setVisible(fn () => Gate::allows('access.telescope'));
    }

    /**
     * Create the Horizon menu item.
     */
    private function createHorizonMenuItem(): MenuItem
    {
        return (new MenuItem)
            ->setLabel(__('Horizon'))
            ->setUrl(route('horizon.index'))
            ->setIcon('queue-list')
            ->setDescription(__('Access Laravel Horizon to monitor and manage queues'))
            ->setTooltip(__('Horizon'))
            ->setTarget('_blank')
            ->setVisible(fn () => Gate::allows('access.horizon'));
    }
}
