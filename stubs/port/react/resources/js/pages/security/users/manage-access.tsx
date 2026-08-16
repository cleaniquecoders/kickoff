import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge, Button, Dialog } from '@/components/ui/*'; // split per shadcn convention
import { Check } from 'lucide-react';
import { useState } from 'react';

type Props = {
    showing?: any;
    userUuid?: any;
};

export default function SecurityUsersManageAccess(props: Props) {
    const [showing, setShowing] = useState<any>(props.showing);
    const [userUuid, setUserUuid] = useState<any>(props.userUuid);
    function handleClose(...args: any[]) {
        router.post(route('port.close'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleOpen(...args: any[]) {
        router.post(route('port.open'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleTogglePermission(...args: any[]) {
        router.post(route('port.togglePermission'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleToggleRole(...args: any[]) {
        router.post(route('port.toggleRole'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="SecurityUsersManageAccess" />
<Dialog open={showing} onOpenChange={setShowing}>
    <DialogContent className="max-w-lg">
        {/* TODO: port @if($this->user) */}
        <div className="space-y-6" x-data="{ tab: 'roles' }">
        {/*User Info */}
        <div className="flex items-center gap-3">
        <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
        <span className="text-sm font-medium text-zinc-600 dark:text-zinc-300">
        {user.initials()}
        </span>
        </div>
        <div className="min-w-0 flex-1">
        <h2 className="text-xl font-semibold truncate">
            <div className="truncate text-sm text-zinc-500 dark:text-zinc-400">{user.email}</div>
            </div>
            <Badge variant="outline" data-color="{{ $this->user->status()->badgeColor() }}">
                {user.status().label()}
            </Badge>
            </div>
            {/*Tabs (Alpine — flux:tabs is Pro-only) */}
            <div className="border-b border-zinc-200 dark:border-zinc-700">
            <nav className="-mb-px flex gap-6">
            <button type="button"
            :class="tab === 'roles'
            ? 'border-accent text-accent-content'
            : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
            className="cursor-pointer border-b-2 px-1 pb-3 text-sm font-medium">
            Roles
            </button>
            {/* TODO: port @can */}
            <button type="button"
            :class="tab === 'permissions'
            ? 'border-accent text-accent-content'
            : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200'"
            className="cursor-pointer border-b-2 px-1 pb-3 text-sm font-medium">
            Permissions
            </button>
            {/* TODO: port @endcan */}
            </nav>
            </div>
            {/*Roles Tab */}
            <div x-show="tab === 'roles'" className="space-y-2">
            {/* TODO: port @foreach */}
            {/* TODO: port @php block */}
            <div onClick={() => handleToggleRole('{role.name}')}
            className="flex cursor-pointer items-center justify-between rounded-lg border border-zinc-200 px-4 py-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
            <div>
            <div className="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {role.display_name ?? role.name}
            </div>
            {/* TODO: port @if($role->description) */}
            <div className="text-xs text-zinc-500 dark:text-zinc-400">{role.description}</div>
            {/* TODO: port @endif */}
            </div>
            {/* TODO: port @if($hasRole) */}
            <div className="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
            <Check className="size-4" />
            </div>
            {/* TODO: port @else */}
            <div className="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
            {/* TODO: port @endif */}
            </div>
            {/* TODO: port @endforeach */}
            </div>
            {/*Permissions Tab */}
            {/* TODO: port @can */}
            <div x-show="tab === 'permissions'" x-cloak className="space-y-4">
            <p className="text-sm text-muted-foreground text-sm">
                Direct permissions apply on top of role permissions. Role-inherited permissions are managed on the role.
            </p>
            {/* TODO: port @foreach */}
            <div className="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
            wire:key="module-{module}">
            <div className="bg-zinc-50 px-4 py-2 font-medium capitalize text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
            {str_replace(.-', '_, ' ', module)}
            </div>
            <div className="divide-y divide-zinc-200 dark:divide-zinc-700">
            {/* TODO: port @foreach */}
            {/* TODO: port @php block */}
            $inherited = $this->inheritedPermissionIds->contains($permission->id);
            $direct = $this->user->permissions->contains('id', $permission->id);
            {/* TODO: port @endphp */}
            <div
            {/* TODO: port @unless */}
            className="flex items-center justify-between px-4 py-3 {inherited ? 'opacity-60' : 'cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800'}">
            <div>
            <div className="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {permission.display_name ?? permission.name}
            </div>
            {/* TODO: port @if($inherited) */}
            <div className="text-xs italic text-zinc-500 dark:text-zinc-400">
            {__('via :roles', .roles' => inheritedVia.get(permission.id)])}
            </div>
            {/* TODO: port @elseif */}
            <div className="text-xs text-zinc-500 dark:text-zinc-400">
            {permission.description}
            </div>
            {/* TODO: port @endif */}
            </div>
            {/* TODO: port @if($inherited || $direct) */}
            <div
            className="flex h-5 w-5 items-center justify-center rounded {inherited ? 'bg-zinc-400 dark:bg-zinc-600' : 'bg-blue-500'} text-white">
            <Check className="size-4" />
            </div>
            {/* TODO: port @else */}
            <div className="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
            {/* TODO: port @endif */}
            </div>
            {/* TODO: port @endforeach */}
            </div>
            </div>
            {/* TODO: port @endforeach */}
            </div>
            {/* TODO: port @endcan */}
            <div className="flex justify-end">
            <Button variant="ghost" onClick={() => handleClose()} className="">
                Close
            </Button>
            </div>
            </div>
            {/* TODO: port @endif */}
        </DialogContent>
    </Dialog>
        </AppLayout>
    );
}