import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useState } from 'react';

type Props = Record<string, never>;

export default function PagesChangelog(props: Props) {


    return (
        <AppLayout>
            <Head title="PagesChangelog" />
<div className="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
<div className="space-y-2">
<h1 className="text-2xl font-semibold tracking-tight">
    {/* TODO: port <x-icon> */}
    Changelog
</h2>
<p className="text-sm text-muted-foreground">
    Stay up to date with the latest updates, improvements, and bug fixes
</p>
</div>
{/*Latest Version */}
<div className="rounded-lg border-2 border-blue-200 bg-blue-50 p-6 shadow-sm dark:border-blue-800 dark:bg-blue-900/20">
<div className="mb-4 flex items-start justify-between">
<div>
<div className="mb-2 flex items-center gap-2">
<h2 className="text-2xl font-bold text-gray-900 dark:text-white">Version 1.0.0</h2>
<span className="inline-flex items-center rounded-full bg-blue-600 px-2.5 py-0.5 text-xs font-medium text-white">
Latest
</span>
</div>
<p className="text-sm text-gray-600 dark:text-gray-400">Released on November 8, 2025</p>
</div>
</div>
<div className="space-y-4">
{/*New Features */}
<div>
<h3 className="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
{/* TODO: port <x-icon> */}
New Features
</h3>
<ul className="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
<li>• Added static pages for Documentation, Support, and Changelog</li>
<li>• Enhanced sidebar navigation with quick action menu items</li>
<li>• Implemented responsive design for all static pages</li>
</ul>
</div>
{/*Improvements */}
<div>
<h3 className="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
{/* TODO: port <x-icon> */}
Improvements
</h3>
<ul className="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
<li>• Improved menu builder architecture with better separation of concerns</li>
<li>• Enhanced user interface with consistent styling across pages</li>
<li>• Optimized page load performance</li>
</ul>
</div>
</div>
</div>
{/*Previous Versions */}
<div className="space-y-4">
{/*Version 0.9.0 */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4">
<h2 className="mb-2 text-xl font-bold text-gray-900 dark:text-white">Version 0.9.0</h2>
<p className="text-sm text-gray-600 dark:text-gray-400">Released on October 15, 2025</p>
</div>
<div className="space-y-4">
{/*New Features */}
<div>
<h3 className="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
{/* TODO: port <x-icon> */}
New Features
</h3>
<ul className="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
<li>• Initial release of the application</li>
<li>• User authentication and authorization system</li>
<li>• Dashboard with basic analytics</li>
<li>• Role-based access control</li>
</ul>
</div>
{/*Bug Fixes */}
<div>
<h3 className="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
{/* TODO: port <x-icon> */}
Bug Fixes
</h3>
<ul className="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
<li>• Fixed login redirect issues</li>
<li>• Resolved dark mode inconsistencies</li>
<li>• Corrected responsive layout issues on mobile devices</li>
</ul>
</div>
</div>
</div>
{/*Version 0.8.0 */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<div className="mb-4">
<h2 className="mb-2 text-xl font-bold text-gray-900 dark:text-white">Version 0.8.0</h2>
<p className="text-sm text-gray-600 dark:text-gray-400">Released on September 20, 2025</p>
</div>
<div className="space-y-4">
{/*Improvements */}
<div>
<h3 className="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
{/* TODO: port <x-icon> */}
Improvements
</h3>
<ul className="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
<li>• Beta release for testing</li>
<li>• Performance optimizations</li>
<li>• UI/UX refinements based on feedback</li>
</ul>
</div>
{/*Known Issues */}
<div>
<h3 className="mb-2 flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
{/* TODO: port <x-icon> */}
Known Issues
</h3>
<ul className="ml-7 space-y-1 text-sm text-gray-700 dark:text-gray-300">
<li>• Some features still in development</li>
<li>• Documentation incomplete</li>
</ul>
</div>
</div>
</div>
</div>
{/*Legend */}
<div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
<h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Change Types</h2>
<div className="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
<div className="flex items-center gap-2">
{/* TODO: port <x-icon> */}
<span className="text-sm text-gray-700 dark:text-gray-300">New Features</span>
</div>
<div className="flex items-center gap-2">
{/* TODO: port <x-icon> */}
<span className="text-sm text-gray-700 dark:text-gray-300">Improvements</span>
</div>
<div className="flex items-center gap-2">
{/* TODO: port <x-icon> */}
<span className="text-sm text-gray-700 dark:text-gray-300">Bug Fixes</span>
</div>
<div className="flex items-center gap-2">
{/* TODO: port <x-icon> */}
<span className="text-sm text-gray-700 dark:text-gray-300">Known Issues</span>
</div>
</div>
</div>
</div>
        </AppLayout>
    );
}