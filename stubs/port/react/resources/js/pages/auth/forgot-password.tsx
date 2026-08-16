import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthForgotPassword(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthForgotPassword" />
<div className="flex flex-col gap-6">
{/* TODO: port <x-auth-header> */}
{/*Session Status */}
{/* TODO: port <x-auth-session-status> */}
<form method="POST" action="{/* TODO: route('password.email') */}" className="flex flex-col gap-6">
{/* TODO: port @csrf */}
{/*Email Address */}
<div className="grid gap-2">
    <Label htmlFor="email">Email Address</Label>
    <Input id="email" type="email" />
</div>
<Button className="w-full">
    Email password reset link
</Button>
</form>
<div className="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
<span>Or, return to</span>
{/* TODO: port route() in link href */}
<Link href="#" className="text-sm underline underline-offset-4">
    </div>
    </div>
        </AppLayout>
    );
}