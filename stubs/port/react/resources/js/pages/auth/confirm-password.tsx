import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthConfirmPassword(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthConfirmPassword" />
<div className="flex flex-col gap-6">
{/* TODO: port <x-auth-header> */}
:title="__('Confirm password')"
:description="__('This is a secure area of the application. Please confirm your password before continuing.')"
/>
{/* TODO: port <x-auth-session-status> */}
<form method="POST" action="{/* TODO: route('password.confirm.store') */}" className="flex flex-col gap-6">
{/* TODO: port @csrf */}
<div className="grid gap-2">
    <Label htmlFor="password">Password</Label>
    <Input id="password" type="password" />
</div>
<Button className="w-full">
    Confirm
</Button>
</form>
</div>
        </AppLayout>
    );
}