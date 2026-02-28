<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.enabled', true);
        $this->migrator->add('notification.channels', ['database']);
    }
};
