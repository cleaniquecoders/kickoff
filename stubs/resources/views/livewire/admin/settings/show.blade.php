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
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <flux:select
                                label="Mail Driver"
                                wire:model="settings.email.mail_mailer"
                            >
                                <option value="smtp">SMTP</option>
                                <option value="sendmail">Sendmail</option>
                                <option value="mailgun">Mailgun</option>
                                <option value="ses">Amazon SES</option>
                                <option value="log">Log (Testing)</option>
                            </flux:select>
                        </div>

                        <div>
                            <flux:select
                                label="Encryption"
                                wire:model="settings.email.mail_encryption"
                            >
                                <option value="">None</option>
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                            </flux:select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <flux:input
                                label="Mail Host"
                                wire:model="settings.email.mail_host"
                                placeholder="smtp.gmail.com"
                            />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">SMTP server address (MAIL_HOST)</p>
                        </div>

                        <div>
                            <flux:input
                                label="Mail Port"
                                type="number"
                                wire:model="settings.email.mail_port"
                                placeholder="587"
                            />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">SMTP port (MAIL_PORT)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <flux:input
                                label="Username"
                                wire:model="settings.email.mail_username"
                                placeholder="your-email@example.com"
                            />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">SMTP username (MAIL_USERNAME)</p>
                        </div>

                        <div>
                            <flux:input
                                label="Password"
                                type="password"
                                wire:model="settings.email.mail_password"
                                placeholder="••••••••"
                            />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">SMTP password (MAIL_PASSWORD)</p>
                        </div>
                    </div>

                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-6">
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white mb-4">Sender Information</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <flux:input
                                    label="From Address"
                                    type="email"
                                    wire:model="settings.email.mail_from_address"
                                    placeholder="noreply@example.com"
                                />
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Default sender email (MAIL_FROM_ADDRESS)</p>
                            </div>

                            <div>
                                <flux:input
                                    label="From Name"
                                    wire:model="settings.email.mail_from_name"
                                    placeholder="Application Name"
                                />
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Default sender name (MAIL_FROM_NAME)</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-700 pt-6">
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
