import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useState } from 'react';

type Props = Record<string, never>;

export default function Welcome(props: Props) {


    return (
        <AppLayout>
            <Head title="Welcome" />
{/* TODO: port <!DOCTYPE html> */}
<html lang="{str_replace('_', '-', app().getLocale())}" className="dark">
<head>
{/* TODO: port @include('partials.head') */}
</head>
<body className="min-h-screen bg-white antialiased dark:bg-zinc-900">
{/* TODO: port @include('partials.analytics-noscript') */}
{/*Navigation */}
<nav className="fixed top-0 z-50 w-full border-b border-zinc-200 bg-white/80 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/80">
<div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
<div className="flex h-16 items-center justify-between">
{/*Logo */}
<a href="{/* TODO: route('home') */}" className="flex items-center space-x-2">
{/* TODO: port <x-kickoff-logo> */}
<span className="text-lg font-bold text-zinc-900 dark:text-white">{/* TODO: config('app.name') */}</span>
</a>
{/*Auth Links */}
<div className="flex items-center space-x-4">
{/* TODO: port @auth */}
<Link href="{/* TODO: route('dashboard') */}" className="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" >
Dashboard
</a>
{/* TODO: port @else */}
<Link href="{/* TODO: route('login') */}" className="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" >
Log in
</a>
{/* TODO: port @if(Route::has('register')) */}
<Link href="{/* TODO: route('register') */}" className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900" >
Get Started
</a>
{/* TODO: port @endif */}
{/* TODO: port @endauth */}
</div>
</div>
</div>
</nav>
{/*Hero Section */}
<section className="relative overflow-hidden pt-32 pb-20 sm:pt-40 sm:pb-32">
<div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
<div className="text-center">
{/*Badge */}
<div className="mb-6 inline-flex items-center rounded-full border border-zinc-200 bg-zinc-50 px-4 py-1.5 dark:border-zinc-700 dark:bg-zinc-800">
{/* TODO: port <x-lucide-sparkles> */}
<span className="text-sm font-medium text-zinc-600 dark:text-zinc-300">Powered by Kickoff</span>
</div>
{/*Heading */}
<h1 className="text-4xl font-bold tracking-tight text-zinc-900 sm:text-6xl dark:text-white">
Build something
<span className="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">amazing</span>
</h1>
{/*Subheading */}
<p className="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
A modern Laravel application bootstrapped with best practices, pre-configured packages, and a solid foundation for your next project.
</p>
{/*CTA Buttons */}
<div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
{/* TODO: port @auth */}
<Link href="{/* TODO: route('dashboard') */}" className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900" >
Go to Dashboard
{/* TODO: port <x-lucide-arrow-right> */}
</a>
{/* TODO: port @else */}
{/* TODO: port @if(Route::has('register')) */}
<Link href="{/* TODO: route('register') */}" className="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900" >
Get Started Free
{/* TODO: port <x-lucide-arrow-right> */}
</a>
{/* TODO: port @endif */}
<Link href="{/* TODO: route('login') */}" className="inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-6 py-3 text-base font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:focus:ring-offset-zinc-900" >
Sign In
</a>
{/* TODO: port @endauth */}
</div>
</div>
</div>
{/*Background Decoration */}
<div className="absolute inset-0 -z-10 overflow-hidden">
<div className="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2">
<div className="h-[500px] w-[500px] rounded-full bg-gradient-to-br from-blue-400/20 to-cyan-400/20 blur-3xl dark:from-blue-600/10 dark:to-cyan-600/10"></div>
</div>
</div>
</section>
{/*Features Section */}
<section className="py-20 sm:py-32">
<div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
<div className="text-center">
<h2 className="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
Everything you need to get started
</h2>
<p className="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
Built with modern tools and best practices to help you ship faster.
</p>
</div>
<div className="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
{/*Feature 1 */}
<div className="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
<div className="mb-4 inline-flex rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
{/* TODO: port <x-lucide-shield-check> */}
</div>
<h3 className="text-lg font-semibold text-zinc-900 dark:text-white">Authentication Ready</h3>
<p className="mt-2 text-zinc-600 dark:text-zinc-400">
Complete authentication system with login, registration, password reset, and email verification.
</p>
</div>
{/*Feature 2 */}
<div className="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
<div className="mb-4 inline-flex rounded-lg bg-green-100 p-3 dark:bg-green-900/50">
{/* TODO: port <x-lucide-users> */}
</div>
<h3 className="text-lg font-semibold text-zinc-900 dark:text-white">Role & Permissions</h3>
<p className="mt-2 text-zinc-600 dark:text-zinc-400">
Flexible role-based access control powered by Spatie Laravel Permission.
</p>
</div>
{/*Feature 3 */}
<div className="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
<div className="mb-4 inline-flex rounded-lg bg-purple-100 p-3 dark:bg-purple-900/50">
{/* TODO: port <x-lucide-activity> */}
</div>
<h3 className="text-lg font-semibold text-zinc-900 dark:text-white">Activity Logging</h3>
<p className="mt-2 text-zinc-600 dark:text-zinc-400">
Track all user activities with comprehensive audit trail using Spatie Activity Log.
</p>
</div>
{/*Feature 4 */}
<div className="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
<div className="mb-4 inline-flex rounded-lg bg-amber-100 p-3 dark:bg-amber-900/50">
{/* TODO: port <x-lucide-zap> */}
</div>
<h3 className="text-lg font-semibold text-zinc-900 dark:text-white">Livewire Powered</h3>
<p className="mt-2 text-zinc-600 dark:text-zinc-400">
Interactive UI components built with Livewire 4 and Flux for a modern experience.
</p>
</div>
{/*Feature 5 */}
<div className="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
<div className="mb-4 inline-flex rounded-lg bg-cyan-100 p-3 dark:bg-cyan-900/50">
{/* TODO: port <x-lucide-database> */}
</div>
<h3 className="text-lg font-semibold text-zinc-900 dark:text-white">Media Management</h3>
<p className="mt-2 text-zinc-600 dark:text-zinc-400">
File upload support with Spatie Media Library, ready for S3 and cloud storage. UI coming soon.
</p>
</div>
{/*Feature 6 */}
<div className="rounded-xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-800/50">
<div className="mb-4 inline-flex rounded-lg bg-rose-100 p-3 dark:bg-rose-900/50">
{/* TODO: port <x-lucide-palette> */}
</div>
<h3 className="text-lg font-semibold text-zinc-900 dark:text-white">Dark Mode</h3>
<p className="mt-2 text-zinc-600 dark:text-zinc-400">
Beautiful light and dark themes with smooth transitions and system preference detection.
</p>
</div>
</div>
</div>
</section>
{/*Tech Stack Section */}
<section className="border-t border-zinc-200 bg-zinc-50 py-16 dark:border-zinc-800 dark:bg-zinc-800/30">
<div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
<p className="text-center text-sm font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
Built with
</p>
<div className="mt-8 flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
<span className="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Laravel</span>
<span className="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Livewire</span>
<span className="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Tailwind CSS</span>
<span className="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Alpine.js</span>
<span className="text-lg font-semibold text-zinc-400 dark:text-zinc-500">Flux</span>
</div>
</div>
</section>
{/*Footer */}
<footer className="border-t border-zinc-200 py-12 dark:border-zinc-800">
<div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
<div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
<div className="flex items-center space-x-2">
{/* TODO: port <x-kickoff-logo> */}
<span className="font-semibold text-zinc-900 dark:text-white">{/* TODO: config('app.name') */}</span>
</div>
<p className="text-sm text-zinc-500 dark:text-zinc-400">
&copy; {date('Y')} {/* TODO: config('app.name') */}. All rights reserved.
</p>
</div>
</div>
</footer>
{/* TODO: port @fluxScripts */}
</body>
</html>
        </AppLayout>
    );
}