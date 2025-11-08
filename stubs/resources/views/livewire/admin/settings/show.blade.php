<?php

use function Livewire\Volt\{state, mount};

state(['section', 'settings' => []]);

mount(function ($section) {
    $this->section = $section;
    // Load settings based on section
    $this->loadSettings();
});

$loadSettings = function () {
    // This is a placeholder - implement based on your settings storage
    $this->settings = [
        'general' => [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ],
        'email' => [
            'mail_driver' => config('mail.default'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ],
        'notifications' => [
            'enabled' => true,
            'channels' => ['mail', 'database'],
        ],
    ];
};

$saveSettings = function () {
    // Implement save logic based on your requirements
    flash()->success('Settings saved successfully.');
};

?>

<div>
    <x-card>
        <x-card.header>
            <flux:heading size="lg">{{ ucfirst($section) }} Settings</flux:heading>
        </x-card.header>
        <x-card.body>
            @if($section === 'general')
                <form wire:submit="saveSettings" class="space-y-6">
                    <div>
                        <flux:input
                            label="Application Name"
                            wire:model="settings.general.app_name"
                            placeholder="Enter application name"
                        />
                    </div>

                    <div>
                        <flux:select
                            label="Environment"
                            wire:model="settings.general.app_env"
                        >
                            <option value="local">Local</option>
                            <option value="development">Development</option>
                            <option value="staging">Staging</option>
                            <option value="production">Production</option>
                        </flux:select>
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="app_debug"
                            wire:model="settings.general.app_debug"
                            class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                        >
                        <label for="app_debug" class="ml-3 text-sm font-medium text-zinc-900 dark:text-white">
                            Debug Mode
                        </label>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button variant="ghost" :href="route('admin.settings.index')" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Save Settings
                        </flux:button>
                    </div>
                </form>

            @elseif($section === 'email')
                <form wire:submit="saveSettings" class="space-y-6">
                    <div>
                        <flux:select
                            label="Mail Driver"
                            wire:model="settings.email.mail_driver"
                        >
                            <option value="smtp">SMTP</option>
                            <option value="sendmail">Sendmail</option>
                            <option value="mailgun">Mailgun</option>
                            <option value="ses">Amazon SES</option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:input
                            label="From Address"
                            type="email"
                            wire:model="settings.email.mail_from_address"
                            placeholder="noreply@example.com"
                        />
                    </div>

                    <div>
                        <flux:input
                            label="From Name"
                            wire:model="settings.email.mail_from_name"
                            placeholder="Application Name"
                        />
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button variant="ghost" :href="route('admin.settings.index')" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Save Settings
                        </flux:button>
                    </div>
                </form>

            @elseif($section === 'notifications')
                <form wire:submit="saveSettings" class="space-y-6">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="notifications_enabled"
                            wire:model="settings.notifications.enabled"
                            class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                        >
                        <label for="notifications_enabled" class="ml-3 text-sm font-medium text-zinc-900 dark:text-white">
                            Enable Notifications
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-900 dark:text-white mb-3">
                            Notification Channels
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="channel_mail"
                                    value="mail"
                                    wire:model="settings.notifications.channels"
                                    class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                >
                                <label for="channel_mail" class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    Email
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="channel_database"
                                    value="database"
                                    wire:model="settings.notifications.channels"
                                    class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                >
                                <label for="channel_database" class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    Database
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="channel_slack"
                                    value="slack"
                                    wire:model="settings.notifications.channels"
                                    class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                >
                                <label for="channel_slack" class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    Slack
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button variant="ghost" :href="route('admin.settings.index')" wire:navigate>
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Save Settings
                        </flux:button>
                    </div>
                </form>

            @else
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Settings section not found.
                </p>
            @endif
        </x-card.body>
    </x-card>
</div>
