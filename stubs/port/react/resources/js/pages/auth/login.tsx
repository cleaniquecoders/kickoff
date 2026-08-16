import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Checkbox, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthLogin(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthLogin" />
<div className="flex flex-col gap-6">
{/* TODO: port <x-auth-header> */}
{/*Session Status */}
{/* TODO: port <x-auth-session-status> */}
<form method="POST" action="{/* TODO: route('login.store') */}" className="flex flex-col gap-6">
{/* TODO: port @csrf */}
{/*Email Address */}
<div className="grid gap-2">
    <Label htmlFor="email">Email address</Label>
    <Input id="email" type="email" />
</div>
{/*Password */}
<div className="relative">
<div className="grid gap-2">
    <Label htmlFor="password">Password</Label>
    <Input id="password" type="password" />
</div>
{/* TODO: port @if(Route::has('password.request')) */}
{/* TODO: port route() in link href */}
<Link href="#" className="text-sm underline underline-offset-4 absolute top-0 text-sm end-0">
    Forgot your password?
</Link>
{/* TODO: port @endif */}
</div>
{/*Remember Me */}
<div className="flex items-center gap-2">
    <Checkbox />
    <Label>Remember me</Label>
</div>
<div className="flex items-center justify-end">
<Button className="w-full">
    Log in
</Button>
</div>
</form>
{/* TODO: port @if(Route::has('register') && config('admin.public_registration', true)) */}
<div className="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
<span>Don\'t have an account?</span>
{/* TODO: port route() in link href */}
<Link href="#" className="text-sm underline underline-offset-4">
    </div>
    {/* TODO: port @endif */}
    </div>
        </AppLayout>
    );
}