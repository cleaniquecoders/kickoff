import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthResetPassword(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthResetPassword" />
<div className="flex flex-col gap-6">
{/* TODO: port <x-auth-header> */}
{/*Session Status */}
{/* TODO: port <x-auth-session-status> */}
<form method="POST" action="{/* TODO: route('password.update') */}" className="flex flex-col gap-6">
{/* TODO: port @csrf */}
{/*Token */}
<input type="hidden" name="token" value="{request().route('token')}">
{/*Email Address */}
<div className="grid gap-2">
    <Label htmlFor="email">Email</Label>
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
    Reset password
</Button>
</div>
</form>
</div>
        </AppLayout>
    );
}