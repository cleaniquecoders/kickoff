import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/*'; // split per shadcn convention
import { useState } from 'react';

type Props = Record<string, never>;

export default function SecurityAuditTrailDetail(props: Props) {


    return (
        <AppLayout>
            <Head title="SecurityAuditTrailDetail" />
{/*Shared audit detail markup. Expects: $audit. Used by the show page and the index flyout. */}
{/*Event Info */}
<div className="mb-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
<div className="flex items-center justify-between mb-4">
<div className="flex items-center gap-3">
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
                <span className="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                {class_basename(audit.auditable_type)}
                </span>
                </div>
                <div className="text-sm text-zinc-500 dark:text-zinc-400">
                {audit.created_at.format('F d, Y \a\t g:i A')}
                </div>
                </div>
                <div className="grid grid-cols-2 gap-4 text-sm">
                <div>
                <span className="text-zinc-500 dark:text-zinc-400">Model ID:</span>
                <span className="ml-2 font-medium text-zinc-900 dark:text-zinc-100">{audit.auditable_id}</span>
                </div>
                <div>
                <span className="text-zinc-500 dark:text-zinc-400">User:</span>
                <span className="ml-2 font-medium text-zinc-900 dark:text-zinc-100">
                {audit.user?.name ?? 'System'}
                </span>
                </div>
                <div>
                <span className="text-zinc-500 dark:text-zinc-400">IP Address:</span>
                <span className="ml-2 font-medium text-zinc-900 dark:text-zinc-100">{audit.ip_address ?? 'N/A'}</span>
                </div>
                <div>
                <span className="text-zinc-500 dark:text-zinc-400">User Agent:</span>
                <span className="ml-2 font-medium text-zinc-900 dark:text-zinc-100 truncate block max-w-xs">
                {Str::limit(audit.user_agent ?? 'N/A', 40)}
                </span>
                </div>
                </div>
                </div>
                {/*Old Values */}
                {/* TODO: port @if(! empty($audit->old_values)) */}
                <div className="mb-6 rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-700/50 dark:bg-red-900/20">
                <h2 className="text-xl font-semibold mb-4 text-red-700 dark:text-red-300">
                    <div className="overflow-x-auto">
                    <table className="min-w-full text-sm">
                    <thead>
                    <tr>
                    <th className="text-left py-2 pr-4 font-medium text-red-700 dark:text-red-300">Field</th>
                    <th className="text-left py-2 font-medium text-red-700 dark:text-red-300">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    {/* TODO: port @foreach */}
                    <tr className="border-t border-red-200 dark:border-red-700/50">
                    <td className="py-2 pr-4 font-medium text-red-800 dark:text-red-200">{key}</td>
                    <td className="py-2 text-red-700 dark:text-red-300">
                    {/* TODO: port @if(is_array($value)) */}
                    <pre className="text-xs">{json_encode(value, JSON_PRETTY_PRINT)}</pre>
                    {/* TODO: port @else */}
                    {value ?? 'null'}
                    {/* TODO: port @endif */}
                    </td>
                    </tr>
                    {/* TODO: port @endforeach */}
                    </tbody>
                    </table>
                    </div>
                    </div>
                    {/* TODO: port @endif */}
                    {/*New Values */}
                    {/* TODO: port @if(! empty($audit->new_values)) */}
                    <div className="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-700/50 dark:bg-green-900/20">
                    <h2 className="text-xl font-semibold mb-4 text-green-700 dark:text-green-300">
                        <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                        <thead>
                        <tr>
                        <th className="text-left py-2 pr-4 font-medium text-green-700 dark:text-green-300">Field</th>
                        <th className="text-left py-2 font-medium text-green-700 dark:text-green-300">Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        {/* TODO: port @foreach */}
                        <tr className="border-t border-green-200 dark:border-green-700/50">
                        <td className="py-2 pr-4 font-medium text-green-800 dark:text-green-200">{key}</td>
                        <td className="py-2 text-green-700 dark:text-green-300">
                        {/* TODO: port @if(is_array($value)) */}
                        <pre className="text-xs">{json_encode(value, JSON_PRETTY_PRINT)}</pre>
                        {/* TODO: port @else */}
                        {value ?? 'null'}
                        {/* TODO: port @endif */}
                        </td>
                        </tr>
                        {/* TODO: port @endforeach */}
                        </tbody>
                        </table>
                        </div>
                        </div>
                        {/* TODO: port @endif */}
        </AppLayout>
    );
}