import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthRegister(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthRegister" />
<div className="flex flex-col gap-6">
{/* TODO: port <x-auth-header> */}
{/*Session Status */}
{/* TODO: port <x-auth-session-status> */}
<form method="POST" action="{/* TODO: route('register.store') */}" className="flex flex-col gap-6">
{/* TODO: port @csrf */}
{/*Name */}
<div className="grid gap-2">
    <Label htmlFor="name">Name</Label>
    <Input id="name" type="text" />
</div>
{/*Email Address */}
<div className="grid gap-2">
    <Label htmlFor="email">Email address</Label>
    <Input id="email" type="email" />
</div>
{/*Password */}
<div className="grid gap-2">
    <Label htmlFor="password">Password</Label>
    <Input id="password" type="password" />
</div>
{/*Confirm Password */}
<div className="grid gap-2">
    <Label htmlFor="password_confirmation">Confirm password</Label>
    <Input id="password_confirmation" type="password" />
</div>
<div className="flex items-center justify-end">
<Button className="w-full">
    Create account
</Button>
</div>
</form>
<div className="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
<span>Already have an account?</span>
{/* TODO: port route() in link href */}
<Link href="#" className="text-sm underline underline-offset-4">
    </div>
    </div>
        </AppLayout>
    );
}