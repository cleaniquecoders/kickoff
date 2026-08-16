import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useState } from 'react';

type Props = Record<string, never>;

export default function PagesSupport(props: Props) {


    return (
        <AppLayout>
            <Head title="PagesSupport" />
<div className="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
<div className="space-y-2">
<h1 className="text-2xl font-semibold tracking-tight">
    {/* TODO: port <x-icon> */}
    Support
</h2>
<p className="text-sm text-muted-foreground">
    Get help and support for any issues you\'re experiencing
</p>
</div>
<div className="grid gap-6 md:grid-cols-2">
{/*Contact Support */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Contact Support</h3>
</div>
<p className="mb-4 text-sm text-gray-600 dark:text-gray-400">
Reach out to our support team for personalized assistance.
</p>
<a href="mailto:support@example.com" className="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
{/* TODO: port <x-icon> */}
Email Support
</a>
</div>
{/*Live Chat */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-green-100 p-2 dark:bg-green-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Live Chat</h3>
</div>
<p className="mb-4 text-sm text-gray-600 dark:text-gray-400">
Get instant help from our support team during business hours.
</p>
<button type="button" className="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
{/* TODO: port <x-icon> */}
Start Chat
</button>
</div>
{/*Knowledge Base */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Knowledge Base</h3>
</div>
<p className="mb-4 text-sm text-gray-600 dark:text-gray-400">
Browse our extensive library of help articles and guides.
</p>
<a href="#" className="inline-flex items-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600">
{/* TODO: port <x-icon> */}
Browse Articles
</a>
</div>
{/*Community Forum */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Community Forum</h3>
</div>
<p className="mb-4 text-sm text-gray-600 dark:text-gray-400">
Connect with other users and share tips and solutions.
</p>
<a href="#" className="inline-flex items-center gap-2 rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600">
{/* TODO: port <x-icon> */}
Visit Forum
</a>
</div>
</div>
{/*Support Hours */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Support Hours</h2>
<div className="grid gap-4 md:grid-cols-2">
<div>
<h4 className="mb-2 font-medium text-gray-900 dark:text-white">Email Support</h4>
<p className="text-sm text-gray-600 dark:text-gray-400">24/7 - Response within 24 hours</p>
</div>
<div>
<h4 className="mb-2 font-medium text-gray-900 dark:text-white">Live Chat</h4>
<p className="text-sm text-gray-600 dark:text-gray-400">Monday - Friday, 9:00 AM - 5:00 PM UTC</p>
</div>
</div>
</div>
{/*Submit a Ticket */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Submit a Support Ticket</h2>
<p className="mb-4 text-sm text-gray-600 dark:text-gray-400">
Can\'t find what you\'re looking for? Submit a support ticket and we\'ll get back to you as soon as possible.
</p>
<button type="button" className="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
{/* TODO: port <x-icon> */}
Create Ticket
</button>
</div>
</div>
        </AppLayout>
    );
}