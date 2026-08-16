import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = {
    detailKey?: any;
    detailUuid?: any;
    showDetail?: any;
};

export default function SecurityAuditTrailIndex(props: Props) {
    const [detailKey, setDetailKey] = useState<any>(props.detailKey);
    const [detailUuid, setDetailUuid] = useState<any>(props.detailUuid);
    const [showDetail, setShowDetail] = useState<any>(props.showDetail);
    function handleOpenDetail(...args: any[]) {
        router.post(route('port.openDetail'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="SecurityAuditTrailIndex" />
<div>
{/*Stats */}
<div className="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
<div className="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
<div className="text-xs text-zinc-500 dark:text-zinc-400">Total Logs</div>
<div className="mt-1 text-2xl font-bold">{\App\Models\Audit::count()}</div>
</div>
<div className="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700/50 dark:bg-green-900/20">
<div className="text-xs text-green-700 dark:text-green-300">Created</div>
<div className="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
{\App\Models\Audit::where('event', 'created').count()}
</div>
</div>
<div className="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700/50 dark:bg-blue-900/20">
<div className="text-xs text-blue-700 dark:text-blue-300">Updated</div>
<div className="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">
{\App\Models\Audit::where('event', 'updated').count()}
</div>
</div>
<div className="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700/50 dark:bg-red-900/20">
<div className="text-xs text-red-700 dark:text-red-300">Deleted</div>
<div className="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">
{\App\Models\Audit::where('event', 'deleted').count()}
</div>
</div>
</div>
{/*Audit Logs (click a row to view details) */}
<div className="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800 overflow-hidden">
<table className="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
<thead className="bg-zinc-50 dark:bg-zinc-900">
<tr>
<th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Event</th>
<th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Model</th>
<th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">User</th>
<th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">Date</th>
</tr>
</thead>
<tbody className="divide-y divide-zinc-200 dark:divide-zinc-700">
{/* TODO: port @forelse */}
<tr
wire:click="openDetail('{audit.uuid}')"
className="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
<td className="px-6 py-4">
{/* TODO: port @switch($audit->event) */}
{/* TODO: port @case('created') */}
<Badge variant="outline" data-color="green">
    {/* TODO: port @break */}
    {/* TODO: port @case('updated') */}
    <Badge variant="outline" data-color="blue">
        {/* TODO: port @break */}
        {/* TODO: port @case('deleted') */}
        <Badge variant="outline" data-color="red">
            {/* TODO: port @break */}
            {/* TODO: port @default */}
            <Badge>
                {/* TODO: port @endswitch */}
                </td>
                <td className="px-6 py-4">
                <div className="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                {class_basename(audit.auditable_type)}
                </div>
                <div className="text-xs text-zinc-500 dark:text-zinc-400">
                ID: {audit.auditable_id}
                </div>
                </td>
                <td className="px-6 py-4">
                {/* TODO: port @if($audit->user) */}
                <div className="flex items-center gap-2">
                <div className="h-8 w-8 flex-shrink-0 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                <span className="text-xs font-medium text-zinc-600 dark:text-zinc-300">
                {strtoupper(substr(audit.user.name, 0, 2))}
                </span>
                </div>
                <div className="text-sm text-zinc-900 dark:text-zinc-100">
                {audit.user.name}
                </div>
                </div>
                {/* TODO: port @else */}
                <span className="text-sm text-zinc-400 dark:text-zinc-500 italic">System</span>
                {/* TODO: port @endif */}
                </td>
                <td className="px-6 py-4">
                <div className="text-sm text-zinc-900 dark:text-zinc-100">
                {audit.created_at.format('M d, Y')}
                </div>
                <div className="text-xs text-zinc-500 dark:text-zinc-400">
                {audit.created_at.format('g:i A')}
                </div>
                </td>
                </tr>
                {/* TODO: port @empty */}
                <tr>
                <td colspan="4" className="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                No audit logs found.
                </td>
                </tr>
                {/* TODO: port @endforelse */}
                </tbody>
                </table>
                </div>
                {/*Pagination */}
                <div className="mt-4">
                {audits.links()}
                </div>
                {/*Read-only audit detail flyout (opened by row click) */}
                {/* TODO: port <x-flyout> */}
                {/* TODO: port @php block */}
                {/* TODO: port @if($audit) */}
                <div className="mb-4 space-y-1">
                <h2 className="text-xl font-semibold">
                    <p className="text-sm text-muted-foreground">
                        </div>
                        {/* TODO: port @include('security.audit-trail._detail', ['audit' => $audit]) */}
                        {/* TODO: port @endif */}
                        </div>
        </AppLayout>
    );
}