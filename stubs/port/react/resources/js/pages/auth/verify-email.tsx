import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthVerifyEmail(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthVerifyEmail" />
<div className="mt-4 flex flex-col gap-6">
<p className="text-sm text-muted-foreground text-center">
    Please verify your email address by clicking on the link we just emailed to you.
</p>
{/* TODO: port @if(session('status') == 'verification-link-sent') */}
<p className="text-sm text-muted-foreground text-center font-medium !dark:text-green-400 !text-green-600">
    A new verification link has been sent to the email address you provided during registration.
</p>
{/* TODO: port @endif */}
<div className="flex flex-col items-center justify-between space-y-3">
<form method="POST" action="{/* TODO: route('verification.send') */}">
{/* TODO: port @csrf */}
<Button className="w-full">
    Resend verification email
</Button>
</form>
<form method="POST" action="{/* TODO: route('logout') */}">
{/* TODO: port @csrf */}
<Button variant="ghost" className="text-sm ">
    Log out
</Button>
</form>
</div>
</div>
        </AppLayout>
    );
}