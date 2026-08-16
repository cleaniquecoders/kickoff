import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Alert, Badge, Button, Dialog } from '@/components/ui/*'; // split per shadcn convention
import { Check, DocumentDuplicate, LoaderCircle, QrCode, ShieldCheck, ShieldExclamation, XCircle } from 'lucide-react';
import { useState } from 'react';

type Props = Record<string, never>;

export default function SettingsTwoFactor(props: Props) {


    return (
        <AppLayout>
            <Head title="SettingsTwoFactor" />
{/* TODO: port <?php */}
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;
new class extends Component {
#[Locked]
public bool $twoFactorEnabled;
#[Locked]
public bool $requiresConfirmation;
#[Locked]
public string $qrCodeSvg = '';
#[Locked]
public string $manualSetupKey = '';
public bool $showModal = false;
public bool $showVerificationStep = false;
#[Validate('required|string|size:6', onUpdate: false)]
public string $code = '';
/**
* Mount the component.
*/
public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
{
abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);
if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
$disableTwoFactorAuthentication(auth()->user());
}
$this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
$this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
}
/**
* Enable two-factor authentication for the user.
*/
public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
{
$enableTwoFactorAuthentication(auth()->user());
if (! $this->requiresConfirmation) {
$this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
}
$this->loadSetupData();
$this->showModal = true;
}
/**
* Load the two-factor authentication setup data for the user.
*/
private function loadSetupData(): void
{
$user = auth()->user();
try {
$this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
$this->manualSetupKey = decrypt($user->two_factor_secret);
} catch (Exception) {
$this->addError('setupData', 'Failed to fetch setup data.');
$this->reset('qrCodeSvg', 'manualSetupKey');
}
}
/**
* Show the two-factor verification step if necessary.
*/
public function showVerificationIfNecessary(): void
{
if ($this->requiresConfirmation) {
$this->showVerificationStep = true;
$this->resetErrorBag();
return;
}
$this->closeModal();
}
/**
* Confirm two-factor authentication for the user.
*/
public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
{
$this->validate();
$confirmTwoFactorAuthentication(auth()->user(), $this->code);
$this->closeModal();
$this->twoFactorEnabled = true;
}
/**
* Reset two-factor verification state.
*/
public function resetVerification(): void
{
$this->reset('code', 'showVerificationStep');
$this->resetErrorBag();
}
/**
* Disable two-factor authentication for the user.
*/
public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
{
$disableTwoFactorAuthentication(auth()->user());
$this->twoFactorEnabled = false;
}
/**
* Close the two-factor authentication modal.
*/
public function closeModal(): void
{
$this->reset(
'code',
'manualSetupKey',
'qrCodeSvg',
'showModal',
'showVerificationStep',
);
$this->resetErrorBag();
if (! $this->requiresConfirmation) {
$this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
}
}
/**
* Get the current modal configuration state.
*/
public function getModalConfigProperty(): array
{
if ($this->twoFactorEnabled) {
return [
'title' => __('Two-Factor Authentication Enabled'),
'description' => __('Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.'),
'buttonText' => __('Close'),
];
}
if ($this->showVerificationStep) {
return [
'title' => __('Verify Authentication Code'),
'description' => __('Enter the 6-digit code from your authenticator app.'),
'buttonText' => __('Continue'),
];
}
return [
'title' => __('Enable Two-Factor Authentication'),
'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
'buttonText' => __('Continue'),
];
}
} ?>
<section className="w-full">
{/* TODO: port @include('partials.settings-heading') */}
{/* TODO: port <x-settings.layout> */}
:heading="__('Two Factor Authentication')"
:subheading="__('Manage your two-factor authentication settings')"
>
<div className="flex flex-col w-full mx-auto space-y-6 text-sm">
{/* TODO: port @if($twoFactorEnabled) */}
<div className="space-y-4">
<div className="flex items-center gap-3">
<Badge variant="outline" data-color="green">
    </div>
    <p className="text-sm text-muted-foreground">
        With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.
    </p>
    {/* TODO: port <livewire:settings.two-factor.recovery-codes> */}
    <div className="flex justify-start">
    <Button variant="destructive" onClick={() => handleDisable()}><ShieldExclamation className="size-4" />
        Disable 2FA
    </Button>
    </div>
    </div>
    {/* TODO: port @else */}
    <div className="space-y-4">
    <div className="flex items-center gap-3">
    <Badge variant="outline" data-color="red">
        </div>
        <p className="text-sm text-muted-foreground">
            When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.
        </p>
        <Button onClick={() => handleEnable()}><ShieldCheck className="size-4" />
            Enable 2FA
        </Button>
        </div>
        {/* TODO: port @endif */}
        </div>
        <Dialog open={showModal} onOpenChange={setShowModal}>
            <DialogContent className="max-w-md md:min-w-md">
                <div className="space-y-6">
                <div className="flex flex-col items-center space-y-4">
                <div className="p-0.5 w-auto rounded-full border border-stone-100 dark:border-stone-600 bg-white dark:bg-stone-800 shadow-sm">
                <div className="p-2.5 rounded-full border border-stone-200 dark:border-stone-600 overflow-hidden bg-stone-100 dark:bg-stone-200 relative">
                <div className="flex items-stretch absolute inset-0 w-full h-full divide-x [&>div]:flex-1 divide-stone-200 dark:divide-stone-300 justify-around opacity-50">
                {/* TODO: port @for ($i = 1; $i <= 5; $i++) */}
                <div></div>
                {/* TODO: port @endfor */}
                </div>
                <div className="flex flex-col items-stretch absolute w-full h-full divide-y [&>div]:flex-1 inset-0 divide-stone-200 dark:divide-stone-300 justify-around opacity-50">
                {/* TODO: port @for ($i = 1; $i <= 5; $i++) */}
                <div></div>
                {/* TODO: port @endfor */}
                </div>
                <QrCode className="size-4" />
                </div>
                </div>
                <div className="space-y-2 text-center">
                <h2 className="text-xl font-semibold">
                    <p className="text-sm text-muted-foreground">
                        </div>
                        </div>
                        {/* TODO: port @if($showVerificationStep) */}
                        <div className="space-y-6">
                        <div className="flex flex-col items-center space-y-3">
                        {/* TODO: port <x-input-otp> */}
                        :digits="6"
                        name="code"
                        wire:model="code"
                        autocomplete="one-time-code"
                        />
                        {/* TODO: port @error */}
                        <p className="text-sm text-muted-foreground">
                            {message}
                        </p>
                        {/* TODO: port @enderror */}
                        </div>
                        <div className="flex items-center space-x-3">
                        <Button variant="outline" onClick={() => handleResetVerification()} className="flex-1">
                            Back
                        </Button>
                        <Button onClick={() => handleConfirmTwoFactor()} className="flex-1">
                            Confirm
                        </Button>
                        </div>
                        </div>
                        {/* TODO: port @else */}
                        {/* TODO: port @error */}
                        <Alert>
                            {/* TODO: port @enderror */}
                            <div className="flex justify-center">
                            <div className="relative w-64 overflow-hidden border rounded-lg border-stone-200 dark:border-stone-700 aspect-square">
                            {/* TODO: port @empty */}
                            <div className="absolute inset-0 flex items-center justify-center bg-white dark:bg-stone-700 animate-pulse">
                            <LoaderCircle className="size-4" />
                            </div>
                            {/* TODO: port @else */}
                            <div className="flex items-center justify-center h-full p-4">
                            <div className="bg-white p-3 rounded">
                            {!! $qrCodeSvg !!}
                            </div>
                            </div>
                            {/* TODO: port @endempty */}
                            </div>
                            </div>
                            <div>
                            <Button onClick={() => handleShowVerificationIfNecessary()} className="w-full">
                                {modalConfig.buttonText}
                            </Button>
                            </div>
                            <div className="space-y-4">
                            <div className="relative flex items-center justify-center w-full">
                            <div className="absolute inset-0 w-full h-px top-1/2 bg-stone-200 dark:bg-stone-600"></div>
                            <span className="relative px-2 text-sm bg-white dark:bg-stone-800 text-stone-600 dark:text-stone-400">
                            or, enter the code manually
                            </span>
                            </div>
                            <div
                            className="flex items-center space-x-2"
                            x-data="{
                            copied: false,
                            async copy() {
                            try {
                            await navigator.clipboard.writeText('{manualSetupKey}');
                            this.copied = true;
                            setTimeout(() => this.copied = false, 1500);
                            } catch (e) {
                            console.warn('Could not copy to clipboard');
                            }
                            }
                            }"
                            >
                            <div className="flex items-stretch w-full border rounded-xl dark:border-stone-700">
                            {/* TODO: port @empty */}
                            <div className="flex items-center justify-center w-full p-3 bg-stone-100 dark:bg-stone-700">
                            <LoaderCircle className="size-4" />
                            </div>
                            {/* TODO: port @else */}
                            <input
                            type="text"
                            readonly
                            value="{manualSetupKey}"
                            className="w-full p-3 bg-transparent outline-none text-stone-900 dark:text-stone-100"
                            />
                            <button
                            {/* TODO: port @click="copy()" */}
                            className="px-3 transition-colors border-l cursor-pointer border-stone-200 dark:border-stone-600"
                            >
                            <DocumentDuplicate className="size-4" />
                            <Check className="size-4" />
                            </button>
                            {/* TODO: port @endempty */}
                            </div>
                            </div>
                            </div>
                            {/* TODO: port @endif */}
                            </div>
                        </DialogContent>
                    </Dialog>
                    </section>
        </AppLayout>
    );
}