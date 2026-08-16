import { Head, Link, router } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button, Dialog, Input, Label, Switch, Textarea } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = {
    description?: any;
    displayName?: any;
    editingUuid?: any;
    isEnabled?: any;
    isProtected?: any;
    showing?: any;
};

export default function AdminRolesRoleForm(props: Props) {
    const [description, setDescription] = useState<any>(props.description);
    const [displayName, setDisplayName] = useState<any>(props.displayName);
    const [editingUuid, setEditingUuid] = useState<any>(props.editingUuid);
    const [isEnabled, setIsEnabled] = useState<any>(props.isEnabled);
    const [isProtected, setIsProtected] = useState<any>(props.isProtected);
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
            <Head title="AdminRolesRoleForm" />
<Dialog open={showing} onOpenChange={setShowing}>
    <DialogContent className="max-w-md">
        <div className="space-y-6">
        <div>
        <h2 className="text-xl font-semibold">
            {editingUuid ? __('Edit Role') : __('Add Role')}
        </h2>
        <p className="text-sm text-muted-foreground mt-2">
            {{ $editingUuid
            ? __('Update the role details below. The internal name cannot be changed.')
            : __('The internal name is derived from the display name.') }}
        </p>
        </div>
        <form onSubmit={handleSave}>
            <div className="grid gap-2">
                <Label htmlFor="displayName">Display Name</Label>
                <Input id="displayName" type="text" value={displayName} onChange={(e) => setDisplayName(e.target.value)} />
            </div>
            <Textarea value={description} onChange={(e) => setDescription(e.target.value)} />
            {/* TODO: port @unless */}
            <Switch checked={isEnabled} onCheckedChange={setIsEnabled} />
            <p className="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
            Disabled roles cannot be assigned to users.
            </p>
            {/* TODO: port @endunless */}
            <div className="flex justify-end gap-3">
            <Button variant="ghost" onClick={() => handleClose()} className="">
                Cancel
            </Button>
            <Button className="">
                {editingUuid ? __('Save Changes') : __('Create Role')}
            </Button>
            </div>
            </form>
            </div>
        </DialogContent>
    </Dialog>
        </AppLayout>
    );
}