import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label, Select, Separator } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = {
    section?: any;
    settings?: any;
    testRecipient?: any;
};

export default function AdminSettingsShow(props: Props) {
    const [section, setSection] = useState<any>(props.section);
    const [settings, setSettings] = useState<any>(props.settings);
    const [testRecipient, setTestRecipient] = useState<any>(props.testRecipient);
    function handleLoadSettings(...args: any[]) {
        router.post(route('port.loadSettings'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSaveSettings(...args: any[]) {
        router.post(route('port.saveSettings'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSendTestMail(...args: any[]) {
        router.post(route('port.sendTestMail'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="AdminSettingsShow" />
<div>
{/* TODO: port <x-card> */}
{/* TODO: port <x-card.header> */}
<h2 className="text-xl font-semibold">
    {/* TODO: port <x-card.body> */}
    {/* TODO: port @if($section === 'general') */}
    <form onSubmit={handleSaveSettings}>
        <div>
        <div className="grid gap-2">
            <Label htmlFor="settings.general.site_name">Site Name</Label>
            <Input id="settings.general.site_name" type="text" value={settings.general.site_name} onChange={(e) => setSettings.general.site_name(e.target.value)} />
        </div>
        </div>
        <div>
        <Select value={settings.general.timezone} onValueChange={setSettings.general.timezone}>
            <SelectTrigger><SelectValue /></SelectTrigger>
            <SelectContent>
                {/* TODO: port @foreach */}
                <SelectItem value="{{ $tz }}">
                    {/* TODO: port @endforeach */}
                </SelectContent>
            </Select>
            <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            The application's default timezone, used by all date and time functions.
            </p>
            </div>
            <div className="flex justify-end gap-2">
            <Button variant="ghost">
                Cancel
            </Button>
            <Button>
                Save Settings
            </Button>
            </div>
            </form>
            {/* TODO: port @elseif */}
            <form onSubmit={handleSaveSettings}>
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <Select value={settings.email.mailer} onValueChange={setSettings.email.mailer}>
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="smtp">
                            <SelectItem value="sendmail">
                                <SelectItem value="ses">
                                    <SelectItem value="mailgun">
                                        <SelectItem value="postmark">
                                            <SelectItem value="log">
                                                <SelectItem value="array">
                                                </SelectContent>
                                            </Select>
                                            <Select value={settings.email.encryption} onValueChange={setSettings.email.encryption}>
                                                <SelectTrigger><SelectValue /></SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="">
                                                        <SelectItem value="tls">
                                                            <SelectItem value="ssl">
                                                            </SelectContent>
                                                        </Select>
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="settings.email.host">SMTP Host</Label>
                                                            <Input id="settings.email.host" type="text" value={settings.email.host} onChange={(e) => setSettings.email.host(e.target.value)} />
                                                        </div>
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="settings.email.port">SMTP Port</Label>
                                                            <Input id="settings.email.port" type="number" value={settings.email.port} onChange={(e) => setSettings.email.port(e.target.value)} />
                                                        </div>
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="settings.email.username">Username</Label>
                                                            <Input id="settings.email.username" type="text" value={settings.email.username} onChange={(e) => setSettings.email.username(e.target.value)} />
                                                        </div>
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="settings.email.password">Password</Label>
                                                            <Input id="settings.email.password" type="password" value={settings.email.password} onChange={(e) => setSettings.email.password(e.target.value)} />
                                                        </div>
                                                        <div>
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="settings.email.from_address">From Address</Label>
                                                            <Input id="settings.email.from_address" type="email" value={settings.email.from_address} onChange={(e) => setSettings.email.from_address(e.target.value)} />
                                                        </div>
                                                        <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Default sender email address</p>
                                                        </div>
                                                        <div>
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="settings.email.from_name">From Name</Label>
                                                            <Input id="settings.email.from_name" type="text" value={settings.email.from_name} onChange={(e) => setSettings.email.from_name(e.target.value)} />
                                                        </div>
                                                        <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Default sender display name</p>
                                                        </div>
                                                        </div>
                                                        <Separator />
                                                        {/*Send a test email using the values above (saved or not) to verify delivery. */}
                                                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                                                        <div className="flex-1">
                                                        <div className="grid gap-2">
                                                            <Label htmlFor="testRecipient">Send test email to</Label>
                                                            <Input id="testRecipient" type="email" value={testRecipient} onChange={(e) => setTestRecipient(e.target.value)} />
                                                        </div>
                                                        </div>
                                                        <Button variant="ghost" onClick={() => handleSendTestMail()}>
                                                            <span>Send test</span>
                                                            <span>Sending…</span>
                                                        </Button>
                                                        </div>
                                                        <div className="flex justify-end gap-2">
                                                        <Button variant="ghost">
                                                            Cancel
                                                        </Button>
                                                        <Button>
                                                            Save Settings
                                                        </Button>
                                                        </div>
                                                        </form>
                                                        {/* TODO: port @elseif */}
                                                        <form onSubmit={handleSaveSettings}>
                                                            <div className="flex items-center">
                                                            <input
                                                            type="checkbox"
                                                            id="notifications_enabled"
                                                            wire:model="settings.notifications.enabled"
                                                            className="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                                            >
                                                            <label htmlFor="notifications_enabled" className="ml-3 text-sm font-medium text-zinc-900 dark:text-white">
                                                            Enable Notifications
                                                            </label>
                                                            </div>
                                                            <div>
                                                            <label className="block text-sm font-medium text-zinc-900 dark:text-white mb-3">
                                                            Notification Channels
                                                            </label>
                                                            <div className="space-y-2">
                                                            <div className="flex items-center">
                                                            <input
                                                            type="checkbox"
                                                            id="channel_mail"
                                                            value="mail"
                                                            wire:model="settings.notifications.channels"
                                                            className="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                                            >
                                                            <label htmlFor="channel_mail" className="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                                            Email
                                                            </label>
                                                            </div>
                                                            <div className="flex items-center">
                                                            <input
                                                            type="checkbox"
                                                            id="channel_database"
                                                            value="database"
                                                            wire:model="settings.notifications.channels"
                                                            className="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                                            >
                                                            <label htmlFor="channel_database" className="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                                            Database
                                                            </label>
                                                            </div>
                                                            <div className="flex items-center">
                                                            <input
                                                            type="checkbox"
                                                            id="channel_slack"
                                                            value="slack"
                                                            wire:model="settings.notifications.channels"
                                                            className="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-600"
                                                            >
                                                            <label htmlFor="channel_slack" className="ml-3 text-sm text-zinc-700 dark:text-zinc-300">
                                                            Slack
                                                            </label>
                                                            </div>
                                                            </div>
                                                            </div>
                                                            <div className="flex justify-end gap-2">
                                                            <Button variant="ghost">
                                                                Cancel
                                                            </Button>
                                                            <Button>
                                                                Save Settings
                                                            </Button>
                                                            </div>
                                                            </form>
                                                            {/* TODO: port @else */}
                                                            <p className="text-sm text-zinc-600 dark:text-zinc-400">
                                                            Settings section not found.
                                                            </p>
                                                            {/* TODO: port @endif */}
                                                            </div>
        </AppLayout>
    );
}