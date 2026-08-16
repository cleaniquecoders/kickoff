import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function SettingsProfile(props: Props) {


    return (
        <AppLayout>
            <Head title="SettingsProfile" />
{/* TODO: port <?php */}
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
new class extends Component {
public string $name = '';
public string $email = '';
/**
* Mount the component.
*/
public function mount(): void
{
$this->name = Auth::user()->name;
$this->email = Auth::user()->email;
}
/**
* Determine if the user has an unverified email address.
*/
#[Computed]
public function hasUnverifiedEmail(): bool
{
return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
}
/**
* Determine if the delete user form should be shown.
*/
#[Computed]
public function showDeleteUser(): bool
{
return ! (Auth::user() instanceof MustVerifyEmail) || Auth::user()->hasVerifiedEmail();
}
/**
* Update the profile information for the currently authenticated user.
*/
public function updateProfileInformation(): void
{
$user = Auth::user();
$validated = $this->validate([
'name' => ['required', 'string', 'max:255'],
'email' => [
'required',
'string',
'lowercase',
'email',
'max:255',
Rule::unique(User::class)->ignore($user->id)
],
]);
$user->fill($validated);
if ($user->isDirty('email')) {
$user->email_verified_at = null;
}
$user->save();
$this->dispatch('profile-updated', name: $user->name);
}
/**
* Send an email verification notification to the current user.
*/
public function resendVerificationNotification(): void
{
$user = Auth::user();
if ($user->hasVerifiedEmail()) {
$this->redirectIntended(default: route('dashboard', absolute: false));
return;
}
$user->sendEmailVerificationNotification();
Session::flash('status', 'verification-link-sent');
}
}; ?>
<section className="w-full">
{/* TODO: port @include('partials.settings-heading') */}
{/* TODO: port <x-settings.layout> */}
<form onSubmit={handleUpdateProfileInformation}>
    <div className="grid gap-2">
        <Label htmlFor="name">Name</Label>
        <Input id="name" type="text" value={name} onChange={(e) => setName(e.target.value)} />
    </div>
    <div>
    <div className="grid gap-2">
        <Label htmlFor="email">Email</Label>
        <Input id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
    </div>
    {/* TODO: port @if($this->hasUnverifiedEmail) */}
    <div>
    <p className="text-sm text-muted-foreground mt-4">
        Your email address is unverified.
        <Link href="#" className="text-sm underline underline-offset-4 text-sm cursor-pointer">
            Click here to re-send the verification email.
        </Link>
    </p>
    {/* TODO: port @if(session('status') === 'verification-link-sent') */}
    <p className="text-sm text-muted-foreground mt-2 font-medium !dark:text-green-400 !text-green-600">
        A new verification link has been sent to your email address.
    </p>
    {/* TODO: port @endif */}
    </div>
    {/* TODO: port @endif */}
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
    {/* TODO: port @if($this->showDeleteUser) */}
    {/* TODO: port <livewire:settings.delete-user-form> */}
    {/* TODO: port @endif */}
    </section>
        </AppLayout>
    );
}