<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';


defineOptions({ layout: AppLayout });

// const props = defineProps<{ /* TODO: declare Inertia props from controller */ }>();
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
</script>

<template>
    <Head title="Show" />

<!--
TODO (3):
 - x-card blade component — port to kit equivalent or drop
 - x-card.header blade component — port to kit equivalent or drop
 - x-card.body blade component — port to kit equivalent or drop
-->

<div>
    <!-- TODO: x-card -->
        <!-- TODO: x-card.header -->
            <Heading size="lg">{{ ucfirst($section) }} Settings</Heading>
        
        <!-- TODO: x-card.body -->

                <form @submit.prevent="saveSettings" class="space-y-6">
                    <div>
                        <Input
                            label="Site Name"
                            v-model="settings.general.site_name"
                            placeholder="Enter your site name" />
                    </div>

                    <div>
                        <Select label="Timezone" v-model="settings.general.timezone">

                                <SelectItem value="{{ $tz }}">{{ $tz }}</SelectItem>

                        </Select>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            The application's default timezone, used by all date and time functions.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button variant="ghost" :href="route('admin.settings.index')">
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary">
                            Save Settings
                        </Button>
                    </div>
                </form>

                <form @submit.prevent="saveSettings" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <Select label="Mailer" v-model="settings.email.mailer">
                            <SelectItem value="smtp">SMTP</SelectItem>
                            <SelectItem value="sendmail">Sendmail</SelectItem>
                            <SelectItem value="ses">Amazon SES</SelectItem>
                            <SelectItem value="mailgun">Mailgun</SelectItem>
                            <SelectItem value="postmark">Postmark</SelectItem>
                            <SelectItem value="log">Log (no send)</SelectItem>
                            <SelectItem value="array">Array (testing)</SelectItem>
                        </Select>

                        <Select label="Encryption" v-model="settings.email.encryption">
                            <SelectItem value="">None</SelectItem>
                            <SelectItem value="tls">TLS</SelectItem>
                            <SelectItem value="ssl">SSL</SelectItem>
                        </Select>

                        <Input label="SMTP Host" v-model="settings.email.host" placeholder="smtp.mailgun.org" />
                        <Input label="SMTP Port" type="number" v-model="settings.email.port" placeholder="587" />
                        <Input label="Username" v-model="settings.email.username" placeholder="postmaster@example.com" />
                        <Input label="Password" type="password" v-model="settings.email.password" placeholder="••••••••" />

                        <div>
                            <Input label="From Address" type="email" v-model="settings.email.from_address" placeholder="noreply@example.com" />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Default sender email address</p>
                        </div>

                        <div>
                            <Input label="From Name" v-model="settings.email.from_name" placeholder="Application Name" />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Default sender display name</p>
                        </div>
                    </div>

                    <Separator variant="subtle" />

                    
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <Input label="Send test email to" type="email" v-model="testRecipient" placeholder="you@example.com" />
                        </div>
                        <Button type="button" variant="ghost" @click="sendTestMail"
                            /* TODO: wire:loading.attr — use form.processing / inertia progress */ /* TODO: unmapped wire:target="sendTestMail" */>
                            <span /* TODO: wire:loading.remove — use form.processing / inertia progress */ /* TODO: unmapped wire:target="sendTestMail" */>Send test</span>
                            <span /* TODO: wire:loading — use form.processing / inertia progress */ /* TODO: unmapped wire:target="sendTestMail" */>Sending…</span>
                        </Button>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button variant="ghost" :href="route('admin.settings.index')">
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary">
                            Save Settings
                        </Button>
                    </div>
                </form>

                <form @submit.prevent="saveSettings" class="space-y-6">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="notifications_enabled"
                            v-model="settings.notifications.enabled"
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
                                    v-model="settings.notifications.channels"
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
                                    v-model="settings.notifications.channels"
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
                                    v-model="settings.notifications.channels"
                                    class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                >
                                <label for="channel_slack" class="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    Slack
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button variant="ghost" :href="route('admin.settings.index')">
                            Cancel
                        </Button>
                        <Button type="submit" variant="primary">
                            Save Settings
                        </Button>
                    </div>
                </form>

                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Settings section not found.
                </p>

        
    
</div>
</template>