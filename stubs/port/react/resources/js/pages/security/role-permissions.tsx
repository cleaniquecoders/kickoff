import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useState } from 'react';

type Props = {
    role?: any;
    selectedPermissions?: any;
};

export default function SecurityRolePermissions(props: Props) {
    const [role, setRole] = useState<any>(props.role);
    const [selectedPermissions, setSelectedPermissions] = useState<any>(props.selectedPermissions);
    function handleToggleModule(...args: any[]) {
        router.post(route('port.toggleModule'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleTogglePermission(...args: any[]) {
        router.post(route('port.togglePermission'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="SecurityRolePermissions" />
<div>
{/* TODO: port @if($this->permissions->isEmpty()) */}
<p className="text-zinc-500 dark:text-zinc-400 italic">No permissions available.</p>
{/* TODO: port @else */}
<div className="space-y-6">
{/* TODO: port @foreach */}
<div className="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
{/*Module Header */}
<div
wire:click="toggleModule('{module}')"
className="cursor-pointer flex items-center justify-between bg-zinc-50 dark:bg-zinc-900 px-4 py-3 hover:bg-zinc-100 dark:hover:bg-zinc-800"
>
<div className="font-medium text-zinc-900 dark:text-zinc-100 capitalize">
{str_replace(.-', '_, ' ', module)}
</div>
<div className="flex items-center gap-2">
<span className="text-sm text-zinc-500 dark:text-zinc-400">
{collect(modulePermissions).filter(fn(p) => in_array(p.id, selectedPermissions)).count()}
/ {modulePermissions.count()}
</span>
{/* TODO: port @php block */}
$allSelected = collect($modulePermissions)->every(fn($p) => in_array($p->id, $selectedPermissions));
{/* TODO: port @endphp */}
{/* TODO: port @if($allSelected) */}
<div className="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
<svg className="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
</svg>
</div>
{/* TODO: port @else */}
<div className="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
{/* TODO: port @endif */}
</div>
</div>
{/*Permissions List */}
<div className="divide-y divide-zinc-200 dark:divide-zinc-700">
{/* TODO: port @foreach */}
<div
wire:click="togglePermission({permission.id})"
className="cursor-pointer flex items-center justify-between px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800"
>
<div>
<div className="text-sm font-medium text-zinc-700 dark:text-zinc-300">
{permission.display_name ?? permission.name}
</div>
{/* TODO: port @if($permission->description) */}
<div className="text-xs text-zinc-500 dark:text-zinc-400">
{permission.description}
</div>
{/* TODO: port @endif */}
</div>
<div>
{/* TODO: port @if(in_array($permission->id, $selectedPermissions)) */}
<div className="flex h-5 w-5 items-center justify-center rounded bg-blue-500 text-white">
<svg className="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
</svg>
</div>
{/* TODO: port @else */}
<div className="h-5 w-5 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
{/* TODO: port @endif */}
</div>
</div>
{/* TODO: port @endforeach */}
</div>
</div>
{/* TODO: port @endforeach */}
</div>
{/* TODO: port @endif */}
</div>
        </AppLayout>
    );
}