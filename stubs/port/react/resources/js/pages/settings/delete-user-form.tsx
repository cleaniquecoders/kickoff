import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Dialog, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function SettingsDeleteUserForm(props: Props) {


    return (
        <AppLayout>
            <Head title="SettingsDeleteUserForm" />
{/* TODO: port <?php */}
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
new class extends Component {
public string $password = '';
/**
* Delete the currently authenticated user.
*/
public function deleteUser(Logout $logout): void
{
$this->validate([
'password' => ['required', 'string', 'current_password'],
]);
tap(Auth::user(), $logout(...))->delete();
$this->redirect('/', navigate: true);
}
}; ?>
<section className="mt-10 space-y-6">
<div className="relative mb-5">
<h2 className="text-lg font-semibold">
    <p className="text-sm text-muted-foreground">
        </div>
        <DialogTrigger asChild>
            <Button variant="destructive">
                Delete account
            </Button>
        </DialogTrigger>
        <Dialog>
            <DialogContent className="max-w-lg">
                <form onSubmit={handleDeleteUser}>
                    <div>
                    <h2 className="text-xl font-semibold">
                        <p className="text-sm text-muted-foreground">
                            Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                        </p>
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="password">Password</Label>
                            <Input id="password" type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
                        </div>
                        <div className="flex justify-end space-x-2 rtl:space-x-reverse">
                        <DialogClose asChild>
                            <Button variant="filled">
                            </DialogClose>
                            <Button variant="destructive">
                                Delete account
                            </Button>
                            </div>
                            </form>
                        </DialogContent>
                    </Dialog>
                    </section>
        </AppLayout>
    );
}