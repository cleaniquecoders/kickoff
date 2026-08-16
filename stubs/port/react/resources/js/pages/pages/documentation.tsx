import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useState } from 'react';

type Props = Record<string, never>;

export default function PagesDocumentation(props: Props) {


    return (
        <AppLayout>
            <Head title="PagesDocumentation" />
<div className="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
<div className="space-y-2">
<h1 className="text-2xl font-semibold tracking-tight">
    {/* TODO: port <x-icon> */}
    Documentation
</h2>
<p className="text-sm text-muted-foreground">
    Comprehensive guides and documentation for using the application
</p>
</div>
<div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
{/*Getting Started */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Getting Started</h3>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">
Learn the basics and get up and running quickly with our step-by-step guide.
</p>
</div>
{/*User Guide */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-green-100 p-2 dark:bg-green-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">User Guide</h3>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">
Detailed instructions on how to use all features of the application.
</p>
</div>
{/*API Reference */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-purple-100 p-2 dark:bg-purple-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">API Reference</h3>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">
Complete API documentation for developers and integrators.
</p>
</div>
{/*Best Practices */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-yellow-100 p-2 dark:bg-yellow-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Best Practices</h3>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">
Tips and tricks to get the most out of the application.
</p>
</div>
{/*Troubleshooting */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-red-100 p-2 dark:bg-red-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">Troubleshooting</h3>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">
Common issues and solutions to help you resolve problems quickly.
</p>
</div>
{/*FAQs */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4 flex items-center gap-3">
<div className="rounded-lg bg-indigo-100 p-2 dark:bg-indigo-900">
{/* TODO: port <x-icon> */}
</div>
<h3 className="text-lg font-semibold text-gray-900 dark:text-white">FAQs</h3>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">
Frequently asked questions and answers from our community.
</p>
</div>
</div>
{/*Additional Resources */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Additional Resources</h2>
<div className="space-y-3">
<a href="#" className="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
{/* TODO: port <x-icon> */}
<span>Video Tutorials</span>
</a>
<a href="#" className="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
{/* TODO: port <x-icon> */}
<span>Community Forum</span>
</a>
<a href="#" className="flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
{/* TODO: port <x-icon> */}
<span>Blog Articles</span>
</a>
</div>
</div>
</div>
        </AppLayout>
    );
}