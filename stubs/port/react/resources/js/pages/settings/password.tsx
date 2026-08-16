import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function SettingsPassword(props: Props) {


    return (
        <AppLayout>
            <Head title="SettingsPassword" />
{/* TODO: port <?php */}
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
new class extends Component {
public string $current_password = '';
public string $password = '';
public string $password_confirmation = '';
/**
* Update the password for the currently authenticated user.
*/
public function updatePassword(): void
{
try {
$validated = $this->validate([
'current_password' => ['required', 'string', 'current_password'],
'password' => ['required', 'string', Password::defaults(), 'confirmed'],
]);
} catch (ValidationException $e) {
$this->reset('current_password', 'password', 'password_confirmation');
throw $e;
}
Auth::user()->update([
'password' => $validated['password'],
]);
$this->reset('current_password', 'password', 'password_confirmation');
$this->dispatch('password-updated');
}
}; ?>
<section className="w-full">
{/* TODO: port @include('partials.settings-heading') */}
{/* TODO: port <x-settings.layout> */}
<form onSubmit={handleUpdatePassword}>
    <div className="grid gap-2">
        <Label htmlFor="current_password">Current password</Label>
        <Input id="current_password" type="password" value={current_password} onChange={(e) => setCurrent_password(e.target.value)} />
    </div>
    <div className="grid gap-2">
        <Label htmlFor="password">New password</Label>
        <Input id="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
    </div>
    <div className="grid gap-2">
        <Label htmlFor="password_confirmation">Confirm Password</Label>
        <Input id="password_confirmation" type="password" value={password_confirmation} onChange={(e) => setPassword_confirmation(e.target.value)} />
    </div>
    <div className="flex items-center gap-4">
    <div className="flex items-center justify-end">
    <Button className="w-full">
        Save
    </Button>
    </div>
    {/* TODO: port <x-action-message> */}
    Saved.
    </div>
    </form>
    </section>
        </AppLayout>
    );
}