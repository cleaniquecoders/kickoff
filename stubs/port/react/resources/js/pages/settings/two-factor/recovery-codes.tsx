import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Alert, Button } from '@/components/ui/*'; // split per shadcn convention
import { ArrowPath, Eye, EyeSlash, LockClosed, XCircle } from 'lucide-react';
import { useState } from 'react';

type Props = Record<string, never>;

export default function SettingsTwoFactorRecoveryCodes(props: Props) {


    return (
        <AppLayout>
            <Head title="SettingsTwoFactorRecoveryCodes" />
{/* TODO: port <?php */}
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;
new class extends Component {
#[Locked]
public array $recoveryCodes = [];
/**
* Mount the component.
*/
public function mount(): void
{
$this->loadRecoveryCodes();
}
/**
* Generate new recovery codes for the user.
*/
public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
{
$generateNewRecoveryCodes(auth()->user());
$this->loadRecoveryCodes();
}
/**
* Load the recovery codes for the user.
*/
private function loadRecoveryCodes(): void
{
$user = auth()->user();
if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
try {
$this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
} catch (Exception) {
$this->addError('recoveryCodes', 'Failed to load recovery codes');
$this->recoveryCodes = [];
}
}
}
}; ?>
<div
className="py-6 space-y-6 border shadow-sm rounded-xl border-zinc-200 dark:border-white/10"
wire:cloak
x-data="{ showRecoveryCodes: false }"
>
<div className="px-6 space-y-2">
<div className="flex items-center gap-2">
<LockClosed className="size-4" />
<h3 className="text-xl font-semibold">
    </div>
    <p className="text-sm text-muted-foreground">
        Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.
    </p>
    </div>
    <div className="px-6">
    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <Button><Eye className="size-4" />
        View Recovery Codes
    </Button>
    <Button><EyeSlash className="size-4" />
        Hide Recovery Codes
    </Button>
    {/* TODO: port @if(filled($recoveryCodes)) */}
    <Button variant="filled" onClick={() => handleRegenerateRecoveryCodes()}><ArrowPath className="size-4" />
        Regenerate Codes
    </Button>
    {/* TODO: port @endif */}
    </div>
    <div
    x-show="showRecoveryCodes"
    x-transition
    id="recovery-codes-section"
    className="relative overflow-hidden"
    x-bind:aria-hidden="!showRecoveryCodes"
    >
    <div className="mt-3 space-y-3">
    {/* TODO: port @error */}
    <Alert>
        {/* TODO: port @enderror */}
        {/* TODO: port @if(filled($recoveryCodes)) */}
        <div
        className="grid gap-1 p-4 font-mono text-sm rounded-lg bg-zinc-100 dark:bg-white/5"
        role="list"
        aria-label="Recovery codes"
        >
        {/* TODO: port @foreach */}
        <div
        role="listitem"
        className="select-text"
        wire:loading.class="opacity-50 animate-pulse"
        >
        {code}
        </div>
        {/* TODO: port @endforeach */}
        </div>
        <p className="text-sm text-muted-foreground text-xs">
            Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate Codes above.
        </p>
        {/* TODO: port @endif */}
        </div>
        </div>
        </div>
        </div>
        </AppLayout>
    );
}