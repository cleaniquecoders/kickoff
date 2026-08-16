import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge, Button, DropdownMenu, DropdownMenuItem } from '@/components/ui/*'; // split per shadcn convention
import { Ellipsis, Pencil, ShieldCheck, Trash2, {{$role>is_enabled?'x':'check'}} } from 'lucide-react';
import { useState } from 'react';

type Props = {
    detailKey?: any;
    detailUuid?: any;
    showDetail?: any;
};

export default function AdminRolesIndex(props: Props) {
    const [detailKey, setDetailKey] = useState<any>(props.detailKey);
    const [detailUuid, setDetailUuid] = useState<any>(props.detailUuid);
    const [showDetail, setShowDetail] = useState<any>(props.showDetail);
    function handleDelete(...args: any[]) {
        router.post(route('port.delete'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleOpenDetail(...args: any[]) {
        router.post(route('port.openDetail'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleRefreshList(...args: any[]) {
        router.post(route('port.refreshList'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleToggleEnabled(...args: any[]) {
        router.post(route('port.toggleEnabled'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function confirmAction(fn: () => void, message: string) {
        // TODO: replace with AlertDialog per shadcn conventions
        if (window.confirm(message)) fn();
    }

    return (
        <AppLayout>
            <Head title="AdminRolesIndex" />
<div>
<div className="flex justify-end mb-4">
{roles.links()}
</div>
<div className="mt-4 flow-root">
<div className="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
<div className="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
<div className="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
<table className="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
<thead className="bg-zinc-50 dark:bg-zinc-800">
<tr>
<th scope="col"
className="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">
Role
</th>
<th scope="col"
className="hidden px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 md:table-cell dark:text-zinc-100">
Description
</th>
<th scope="col"
className="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
Status
</th>
<th scope="col"
className="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">
Users
</th>
<th scope="col" className="py-3.5 pr-4 pl-3 sm:pr-6">
<span className="sr-only">Actions</span>
</th>
</tr>
</thead>
<tbody className="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
{/* TODO: port @forelse */}
<tr
wire:click="openDetail('{role.uuid}')"
className="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
<td className="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-zinc-900 dark:text-white sm:pl-6">
{role.display_name}
{/* TODO: port @if($role->isProtected()) */}
<Badge variant="outline" data-color="zinc" className="ml-1">
    {/* TODO: port @endif */}
    </td>
    <td className="hidden px-3 py-4 text-sm text-zinc-700 md:table-cell dark:text-zinc-300">
    {role.description ?? '-'}
    </td>
    <td className="px-3 py-4 text-sm">
    <Badge variant="outline" data-color="{{ $role->is_enabled ? 'green' : 'zinc' }}">
        {role.is_enabled ? __('Enabled') : __('Disabled')}
    </Badge>
    </td>
    <td className="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
    {role.users_count}
    </td>
    <td className="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6"
    {/* TODO: port @click.stop> */}
    <DropdownMenu>
        <Button variant="ghost" className=""><Ellipsis className="size-4" />
            <DropdownMenuContent>
                <DropdownMenuItem onClick={() => handleOpenDetail('{{ $role->uuid }}')}><ShieldCheck className="mr-2 size-4" />
                    Manage Permissions
                </DropdownMenuItem>
                {/* TODO: port @can */}
                <DropdownMenuItem><Pencil className="mr-2 size-4" />
                    Edit
                </DropdownMenuItem>
                {/* TODO: port @endcan */}
                {/* TODO: port @unless */}
                {/* TODO: port @can */}
                <DropdownMenuItem onClick={() => handleToggleEnabled('{{ $role->uuid }}')}><{{$role>is_enabled?'x':'check'}} className="mr-2 size-4" />
                    {role.is_enabled ? __('Disable') : __('Enable')}
                </DropdownMenuItem>
                {/* TODO: port @endcan */}
                {/* TODO: port @can */}
                <DropdownMenuItem className="text-destructive" onClick={() => confirmAction(() => handleDelete('{{ $role->uuid }}'), '{{ __(\'Are you sure you want to delete :role?\', [\'role\' => $role->display_name]) }}')}><Trash2 className="mr-2 size-4" />
                    Delete
                </DropdownMenuItem>
                {/* TODO: port @endcan */}
                {/* TODO: port @endunless */}
            </DropdownMenuContent>
        </DropdownMenu>
        </td>
        </tr>
        {/* TODO: port @empty */}
        <tr>
        <td colspan="5" className="py-12 text-center">
        {/* TODO: port <x-empty-state> */}
        title="No roles found"
        description="Get started by creating a new role."
        />
        </td>
        </tr>
        {/* TODO: port @endforelse */}
        </tbody>
        </table>
        </div>
        </div>
        </div>
        </div>
        <div className="mt-4">
        {roles.links()}
        </div>
        {/*Role detail flyout (opened by row click / "Manage Permissions") */}
        {/* TODO: port <x-flyout> */}
        {/* TODO: port @if($detailUuid) */}
        {/* TODO: port <livewire:admin.roles.show> */}
        {/* TODO: port @endif */}
        </div>
        </AppLayout>
    );
}