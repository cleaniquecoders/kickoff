import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AdminSettingsIndex(props: Props) {


    return (
        <AppLayout>
            <Head title="AdminSettingsIndex" />
<h2 className="text-2xl font-semibold tracking-tight mb-6">
    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    {/* TODO: port <x-card> */}
    {/* TODO: port <x-card.header> */}
    <div className="flex items-center">
    {/* TODO: port <x-lucide-globe> */}
    <h2 className="text-xl font-semibold">
        </div>
        {/* TODO: port <x-card.body> */}
        <p className="text-sm text-zinc-600 dark:text-zinc-400">
        Manage your site name and general application settings.
        </p>
        {/* TODO: port <x-card.footer> */}
        <Button>
            Configure
        </Button>
        {/* TODO: port <x-card> */}
        {/* TODO: port <x-card.header> */}
        <div className="flex items-center">
        {/* TODO: port <x-lucide-lock-keyhole> */}
        <h2 className="text-xl font-semibold">
            </div>
            {/* TODO: port <x-card.body> */}
            <p className="text-sm text-zinc-600 dark:text-zinc-400">
            Control how users sign in and whether they can register themselves.
            </p>
            {/* TODO: port <x-card.footer> */}
            <Button>
                Configure
            </Button>
            {/* TODO: port <x-card> */}
            {/* TODO: port <x-card.header> */}
            <div className="flex items-center">
            {/* TODO: port <x-lucide-life-buoy> */}
            <h2 className="text-xl font-semibold">
                </div>
                {/* TODO: port <x-card.body> */}
                <p className="text-sm text-zinc-600 dark:text-zinc-400">
                Embed the g8desk support widget for your users.
                </p>
                {/* TODO: port <x-card.footer> */}
                <Button>
                    Configure
                </Button>
                {/* TODO: port <x-card> */}
                {/* TODO: port <x-card.header> */}
                <div className="flex items-center">
                {/* TODO: port <x-lucide-mail> */}
                <h2 className="text-xl font-semibold">
                    </div>
                    {/* TODO: port <x-card.body> */}
                    <p className="text-sm text-zinc-600 dark:text-zinc-400">
                    Configure the default sender address and name for outgoing emails.
                    </p>
                    {/* TODO: port <x-card.footer> */}
                    <Button>
                        Configure
                    </Button>
                    {/* TODO: port <x-card> */}
                    {/* TODO: port <x-card.header> */}
                    <div className="flex items-center">
                    {/* TODO: port <x-lucide-search> */}
                    <h2 className="text-xl font-semibold">
                        </div>
                        {/* TODO: port <x-card.body> */}
                        <p className="text-sm text-zinc-600 dark:text-zinc-400">
                        Search engine defaults, structured data, robots.txt, and Google Analytics.
                        </p>
                        {/* TODO: port <x-card.footer> */}
                        <Button>
                            Configure
                        </Button>
                        {/* TODO: port <x-card> */}
                        {/* TODO: port <x-card.header> */}
                        <div className="flex items-center">
                        {/* TODO: port <x-lucide-bell> */}
                        <h2 className="text-xl font-semibold">
                            </div>
                            {/* TODO: port <x-card.body> */}
                            <p className="text-sm text-zinc-600 dark:text-zinc-400">
                            Enable or disable notifications and choose delivery channels.
                            </p>
                            {/* TODO: port <x-card.footer> */}
                            <Button>
                                Configure
                            </Button>
                            </div>
        </AppLayout>
    );
}