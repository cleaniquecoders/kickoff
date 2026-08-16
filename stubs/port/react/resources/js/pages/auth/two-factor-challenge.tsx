import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function AuthTwoFactorChallenge(props: Props) {


    return (
        <AppLayout>
            <Head title="AuthTwoFactorChallenge" />
<div className="flex flex-col gap-6">
<div
className="relative w-full h-auto"
x-cloak
x-data="{
showRecoveryInput: @js($errors->has('recovery_code')),
code: '',
recovery_code: '',
toggleInput() {
this.showRecoveryInput = !this.showRecoveryInput;
this.code = '';
this.recovery_code = '';
$dispatch('clear-2fa-auth-code');
$nextTick(() => {
this.showRecoveryInput
? this.$refs.recovery_code?.focus()
: $dispatch('focus-2fa-auth-code');
});
},
}"
>
<div x-show="!showRecoveryInput">
{/* TODO: port <x-auth-header> */}
:title="__('Authentication Code')"
:description="__('Enter the authentication code provided by your authenticator application.')"
/>
</div>
<div x-show="showRecoveryInput">
{/* TODO: port <x-auth-header> */}
:title="__('Recovery Code')"
:description="__('Please confirm access to your account by entering one of your emergency recovery codes.')"
/>
</div>
<form method="POST" action="{/* TODO: route('two-factor.login.store') */}">
{/* TODO: port @csrf */}
<div className="space-y-5 text-center">
<div x-show="!showRecoveryInput">
<div className="flex items-center justify-center my-5">
{/* TODO: port <x-input-otp> */}
name="code"
digits="6"
autocomplete="one-time-code"
x-model="code"
/>
</div>
{/* TODO: port @error */}
<p className="text-sm text-muted-foreground">
    {message}
</p>
{/* TODO: port @enderror */}
</div>
<div x-show="showRecoveryInput">
<div className="my-5">
<div className="grid gap-2">
    <Input id="recovery_code" type="text" />
</div>
</div>
{/* TODO: port @error */}
<p className="text-sm text-muted-foreground">
    {message}
</p>
{/* TODO: port @enderror */}
</div>
<Button className="w-full">
    Continue
</Button>
</div>
<div className="mt-5 space-x-0.5 text-sm leading-5 text-center">
<span className="opacity-50">or you can</span>
<div className="inline font-medium underline cursor-pointer opacity-80">
<span x-show="!showRecoveryInput">login using a recovery code</span>
<span x-show="showRecoveryInput">login using an authentication code</span>
</div>
</div>
</form>
</div>
</div>
        </AppLayout>
    );
}