import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Checkbox, Dialog, Input, Label } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = {
    editingUuid?: any;
    email?: any;
    name?: any;
    roles?: any;
    sendPasswordSetupLink?: any;
    showing?: any;
};

export default function SecurityUsersUserForm(props: Props) {
    const [editingUuid, setEditingUuid] = useState<any>(props.editingUuid);
    const [email, setEmail] = useState<any>(props.email);
    const [name, setName] = useState<any>(props.name);
    const [roles, setRoles] = useState<any>(props.roles);
    const [sendPasswordSetupLink, setSendPasswordSetupLink] = useState<any>(props.sendPasswordSetupLink);
    const [showing, setShowing] = useState<any>(props.showing);
    function handleClose(...args: any[]) {
        router.post(route('port.close'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleOpen(...args: any[]) {
        router.post(route('port.open'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSave(...args: any[]) {
        router.post(route('port.save'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="SecurityUsersUserForm" />
<Dialog open={showing} onOpenChange={setShowing}>
    <DialogContent className="max-w-md">
        <div className="space-y-6">
        <div>
        <h2 className="text-xl font-semibold">
            {editingUuid ? __('Edit User') : __('Add User')}
        </h2>
        <p className="text-sm text-muted-foreground mt-2">
            {{ $editingUuid
            ? __('Update the account details below.')
            : __('The new user will receive a link to set their own password.') }}
        </p>
        </div>
        <form onSubmit={handleSave}>
            <div className="grid gap-2">
                <Label htmlFor="name">Name</Label>
                <Input id="name" type="text" value={name} onChange={(e) => setName(e.target.value)} />
            </div>
            <div className="grid gap-2">
                <Label htmlFor="email">Email</Label>
                <Input id="email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
            </div>
            {/* TODO: port @if($editingUuid) */}
            <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            Changing the email resets the verification status.
            </p>
            {/* TODO: port @endif */}
            <div className="grid gap-2">
                <p className="text-sm font-medium">
                    <div className="space-y-2">
                    {/* TODO: port @foreach */}
                    <div className="flex items-center gap-2">
                        <Checkbox checked={roles} onCheckedChange={setRoles} />
                        <Label>$role->display_name</Label>
                    </div>
                    {/* TODO: port @endforeach */}
                    </div>
                    {/* TODO: port @error */}
                    <p className="text-sm text-muted-foreground mt-2 text-red-500">
                        {/* TODO: port @enderror */}
                    </div>
                    {/* TODO: port @unless */}
                    <div className="flex items-center gap-2">
                        <Checkbox checked={sendPasswordSetupLink} onCheckedChange={setSendPasswordSetupLink} />
                        <Label>Send password setup link</Label>
                    </div>
                    {/* TODO: port @endunless */}
                    <div className="flex justify-end gap-3">
                    <Button variant="ghost" onClick={() => handleClose()} className="">
                        Cancel
                    </Button>
                    <Button className="">
                        {editingUuid ? __('Save Changes') : __('Create User')}
                    </Button>
                    </div>
                    </form>
                    </div>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}