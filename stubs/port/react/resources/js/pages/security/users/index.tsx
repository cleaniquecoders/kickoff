import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge, Button, Checkbox, DropdownMenu, DropdownMenuItem, Input, Label, Select, Separator } from '@/components/ui/*'; // split per shadcn convention
import { Ellipsis, KeyRound, MagnifyingGlass, MailCheck, Pencil, RotateCcw, ShieldCheck, Trash2, UserCheck, UserX } from 'lucide-react';
import { useState } from 'react';

type Props = {
    bulkRole?: any;
    detailKey?: any;
    detailUuid?: any;
    roleFilter?: any;
    search?: any;
    selectPage?: any;
    selected?: any;
    showDetail?: any;
    statusFilter?: any;
};

export default function SecurityUsersIndex(props: Props) {
    const [bulkRole, setBulkRole] = useState<any>(props.bulkRole);
    const [detailKey, setDetailKey] = useState<any>(props.detailKey);
    const [detailUuid, setDetailUuid] = useState<any>(props.detailUuid);
    const [roleFilter, setRoleFilter] = useState<any>(props.roleFilter);
    const [search, setSearch] = useState<any>(props.search);
    const [selectPage, setSelectPage] = useState<any>(props.selectPage);
    const [selected, setSelected] = useState<any>(props.selected);
    const [showDetail, setShowDetail] = useState<any>(props.showDetail);
    const [statusFilter, setStatusFilter] = useState<any>(props.statusFilter);
    function handleActivate(...args: any[]) {
        router.post(route('port.activate'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleBulkAssignRole(...args: any[]) {
        router.post(route('port.bulkAssignRole'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleBulkDelete(...args: any[]) {
        router.post(route('port.bulkDelete'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleClearSelection(...args: any[]) {
        router.post(route('port.clearSelection'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleDelete(...args: any[]) {
        router.post(route('port.delete'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleOpenDetail(...args: any[]) {
        router.post(route('port.openDetail'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleRefreshList(...args: any[]) {
        router.post(route('port.refreshList'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleResendVerification(...args: any[]) {
        router.post(route('port.resendVerification'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleRestore(...args: any[]) {
        router.post(route('port.restore'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSendPasswordResetLink(...args: any[]) {
        router.post(route('port.sendPasswordResetLink'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleSuspend(...args: any[]) {
        router.post(route('port.suspend'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function confirmAction(fn: () => void, message: string) {
        // TODO: replace with AlertDialog per shadcn conventions
        if (window.confirm(message)) fn();
    }

    return (
        <AppLayout>
            <Head title="SecurityUsersIndex" />
<div>
{/*Stats */}
<div className="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
<div className="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
<div className="text-xs text-zinc-500 dark:text-zinc-400">Total Users</div>
<div className="mt-1 text-2xl font-bold">{stats.total}</div>
</div>
<div className="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700/50 dark:bg-green-900/20">
<div className="text-xs text-green-700 dark:text-green-300">Active</div>
<div className="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{stats.active}</div>
</div>
<div className="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700/50 dark:bg-amber-900/20">
<div className="text-xs text-amber-700 dark:text-amber-300">Suspended</div>
<div className="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{stats.suspended}</div>
</div>
<div className="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700/50 dark:bg-red-900/20">
<div className="text-xs text-red-700 dark:text-red-300">Deleted</div>
<div className="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{stats.deleted}</div>
</div>
</div>
{/*Search & Filters */}
<div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
<div className="flex-1">
<div className="grid gap-2">
    <Input id="field" type="text" />
</div>
</div>
<div className="flex gap-3">
<Select value={roleFilter} onValueChange={setRoleFilter}>
    <SelectTrigger className="sm:w-44"><SelectValue /></SelectTrigger>
    <SelectContent>
        <SelectItem value="">
            {/* TODO: port @foreach */}
            <SelectItem value="{{ $role->name }}">
                {/* TODO: port @endforeach */}
            </SelectContent>
        </Select>
        <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="sm:w-44"><SelectValue /></SelectTrigger>
            <SelectContent>
                <SelectItem value="">
                    {/* TODO: port @foreach */}
                    <SelectItem value="{{ $status->value }}">
                        {/* TODO: port @endforeach */}
                    </SelectContent>
                </Select>
                </div>
                </div>
                {/*Bulk Action Bar */}
                {/* TODO: port @if(count($selected) > 0) */}
                <div
                className="mb-4 flex flex-col gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-3 sm:flex-row sm:items-center dark:border-zinc-700 dark:bg-zinc-800/50">
                <p className="text-sm text-muted-foreground font-medium">
                    {/* TODO: trans_choice(':count user selected|:count users selected', count($selected), ['count' => count($selected)]) */}
                </p>
                <div className="flex-1" />
                <div className="flex flex-wrap items-center gap-2">
                {/* TODO: port @can */}
                <Select value={bulkRole} onValueChange={setBulkRole}>
                    <SelectTrigger className="w-40"><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="">
                            {/* TODO: port @foreach */}
                            <SelectItem value="{{ $role->name }}">
                                {/* TODO: port @endforeach */}
                            </SelectContent>
                        </Select>
                        <Button onClick={() => handleBulkAssignRole()} className="">
                            Apply
                        </Button>
                        {/* TODO: port @endcan */}
                        {/* TODO: port @can */}
                        <Button variant="destructive" onClick={() => confirmAction(() => handleBulkDelete(), '{{ __(\'Delete :count selected user(s)? Protected accounts will be skipped. Deleted accounts can be restored later.\', [\'count\' => count($selected)]) }}')} className=""><Trash2 className="size-4" />
                            Delete
                        </Button>
                        {/* TODO: port @endcan */}
                        <Button variant="ghost" onClick={() => handleClearSelection()} className="">
                            Clear
                        </Button>
                        </div>
                        </div>
                        {/* TODO: port @endif */}
                        {/*Users Table */}
                        <div className="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                        <table className="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead className="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                        <th className="w-10 px-4 py-3">
                        <Checkbox checked={selectPage} onCheckedChange={setSelectPage} />
                        </th>
                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        User</th>
                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        Roles</th>
                        <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        Status</th>
                        <th className="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 md:table-cell dark:text-zinc-300">
                        Joined</th>
                        <th className="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        Actions</th>
                        </tr>
                        </thead>
                        <tbody className="divide-y divide-zinc-200 dark:divide-zinc-700">
                        {/* TODO: port @forelse */}
                        <tr
                        wire:click="openDetail('{user.uuid}')"
                        className="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                        <td className="px-4 py-4" @click.stop>
                        <Checkbox checked={selected} onCheckedChange={setSelected} />
                        </td>
                        <td className="px-4 py-4">
                        <div className="flex items-center gap-3">
                        <div
                        className="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <span className="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                        {user.initials()}
                        </span>
                        </div>
                        <div className="min-w-0">
                        <div className="truncate font-medium text-zinc-900 dark:text-zinc-100">{user.name}
                        </div>
                        <div className="truncate text-sm text-zinc-500 dark:text-zinc-400">{user.email}
                        </div>
                        </div>
                        </div>
                        </td>
                        <td className="px-4 py-4">
                        <div className="flex flex-wrap gap-1">
                        {/* TODO: port @forelse */}
                        <Badge variant="outline" data-color="{{ $role->name === 'superadmin' ? 'red' : ($role->name === 'administrator' ? 'amber' : 'zinc') }}">
                            {role.display_name ?? role.name}
                        </Badge>
                        {/* TODO: port @empty */}
                        <span className="text-sm italic text-zinc-400 dark:text-zinc-500">No roles</span>
                        {/* TODO: port @endforelse */}
                        </div>
                        </td>
                        <td className="px-4 py-4">
                        <Badge variant="outline" data-color="{{ $user->status()->badgeColor() }}">
                            {user.status().label()}
                        </Badge>
                        </td>
                        <td className="hidden px-4 py-4 text-sm text-zinc-500 md:table-cell dark:text-zinc-400">
                        {user.created_at.format('M d, Y')}
                        </td>
                        <td className="px-4 py-4 text-right" @click.stop>
                        <DropdownMenu>
                            <Button variant="ghost" className=""><Ellipsis className="size-4" />
                                <DropdownMenuContent>
                                    {/* TODO: port @if($user->trashed()) */}
                                    {/* TODO: port @can */}
                                    <DropdownMenuItem onClick={() => handleRestore('{{ $user->uuid }}')}><RotateCcw className="mr-2 size-4" />
                                        Restore
                                    </DropdownMenuItem>
                                    {/* TODO: port @endcan */}
                                    {/* TODO: port @else */}
                                    {/* TODO: port @can */}
                                    <DropdownMenuItem><Pencil className="mr-2 size-4" />
                                        Edit
                                    </DropdownMenuItem>
                                    {/* TODO: port @endcan */}
                                    {/* TODO: port @can */}
                                    <DropdownMenuItem><ShieldCheck className="mr-2 size-4" />
                                        Manage Access
                                    </DropdownMenuItem>
                                    {/* TODO: port @endcan */}
                                    {/* TODO: port @if(auth()->user()->canImpersonate() && $user->canBeImpersonated() && auth()->id() !== $user->id) */}
                                    <DropdownMenuItem asChild>
                                        {/* TODO: port route() in menu.item href */}
                                        <Link href="#"><UserCheck className="mr-2 size-4" />
                                            Impersonate
                                        </DropdownMenuItem>
                                        {/* TODO: port @endif */}
                                        {/* TODO: port @can */}
                                        <DropdownMenuItem onClick={() => handleSendPasswordResetLink('{{ $user->uuid }}')}><KeyRound className="mr-2 size-4" />
                                            Send Password Reset
                                        </DropdownMenuItem>
                                        {/* TODO: port @endcan */}
                                        {/* TODO: port @if(! $user->hasVerifiedEmail()) */}
                                        {/* TODO: port @can */}
                                        <DropdownMenuItem onClick={() => handleResendVerification('{{ $user->uuid }}')}><MailCheck className="mr-2 size-4" />
                                            Resend Verification
                                        </DropdownMenuItem>
                                        {/* TODO: port @endcan */}
                                        {/* TODO: port @endif */}
                                        {/* TODO: port @can */}
                                        <Separator /* DropdownMenuSeparator */ />
                                        {/* TODO: port @if($user->isSuspended()) */}
                                        <DropdownMenuItem onClick={() => handleActivate('{{ $user->uuid }}')}><UserCheck className="mr-2 size-4" />
                                            Activate
                                        </DropdownMenuItem>
                                        {/* TODO: port @else */}
                                        <DropdownMenuItem onClick={() => confirmAction(() => handleSuspend('{{ $user->uuid }}'), '{{ __(\':name will no longer be able to sign in. Continue?\', [\'name\' => $user->name]) }}')}><UserX className="mr-2 size-4" />
                                            Suspend
                                        </DropdownMenuItem>
                                        {/* TODO: port @endif */}
                                        {/* TODO: port @endcan */}
                                        {/* TODO: port @can */}
                                        <DropdownMenuItem className="text-destructive" onClick={() => confirmAction(() => handleDelete('{{ $user->uuid }}'), '{{ __(\'Are you sure you want to delete :name? The account can be restored later.\', [\'name\' => $user->name]) }}')}><Trash2 className="mr-2 size-4" />
                                            Delete
                                        </DropdownMenuItem>
                                        {/* TODO: port @endcan */}
                                        {/* TODO: port @endif */}
                                    </DropdownMenuContent>
                                </DropdownMenu>
                                </td>
                                </tr>
                                {/* TODO: port @empty */}
                                <tr>
                                <td colspan="6" className="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No users found.
                                </td>
                                </tr>
                                {/* TODO: port @endforelse */}
                                </tbody>
                                </table>
                                </div>
                                {/*Pagination */}
                                <div className="mt-4">
                                {users.links()}
                                </div>
                                {/*User detail flyout (opened by row click) */}
                                {/* TODO: port <x-flyout> */}
                                {/* TODO: port @php block */}
                                {/* TODO: port @if($u) */}
                                <div className="space-y-6">
                                <div className="flex items-center gap-4">
                                <div className="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                                <span className="text-lg font-semibold text-zinc-600 dark:text-zinc-300">{u.initials()}</span>
                                </div>
                                <div className="min-w-0">
                                <h2 className="text-xl font-semibold">
                                    <p className="text-sm text-muted-foreground">
                                        </div>
                                        </div>
                                        <dl className="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                                        <div>
                                        <dt className="font-medium text-zinc-500 dark:text-zinc-400">Status</dt>
                                        <dd className="mt-1"><flux:badge size="sm" color="{u.status().badgeColor()}">{u.status().label()}</flux:badge></dd>
                                        </div>
                                        <div>
                                        <dt className="font-medium text-zinc-500 dark:text-zinc-400">Email verified</dt>
                                        <dd className="mt-1 text-zinc-900 dark:text-zinc-100">{u.hasVerifiedEmail() ? __('Yes') : __('No')}</dd>
                                        </div>
                                        <div className="col-span-2">
                                        <dt className="font-medium text-zinc-500 dark:text-zinc-400">Roles</dt>
                                        <dd className="mt-1 flex flex-wrap gap-1">
                                        {/* TODO: port @forelse */}
                                        <Badge variant="outline" data-color="{{ $role->name === 'superadmin' ? 'red' : ($role->name === 'administrator' ? 'amber' : 'zinc') }}">
                                            {/* TODO: port @empty */}
                                            <span className="italic text-zinc-400 dark:text-zinc-500">No roles</span>
                                            {/* TODO: port @endforelse */}
                                            </dd>
                                            </div>
                                            <div>
                                            <dt className="font-medium text-zinc-500 dark:text-zinc-400">Joined</dt>
                                            <dd className="mt-1 text-zinc-900 dark:text-zinc-100">{u.created_at.format('M d, Y')}</dd>
                                            </div>
                                            </dl>
                                            <Separator />
                                            <div className="flex flex-wrap gap-2">
                                            {/* TODO: port @if($u->trashed()) */}
                                            {/* TODO: port @can */}
                                            <Button onClick={() => handleRestore('{{ $u->uuid }}')} className=""><RotateCcw className="size-4" />
                                                {/* TODO: port @endcan */}
                                                {/* TODO: port @else */}
                                                {/* TODO: port @can */}
                                                <Button className=""><Pencil className="size-4" />
                                                    {/* TODO: port @endcan */}
                                                    {/* TODO: port @can */}
                                                    <Button variant="ghost" className=""><ShieldCheck className="size-4" />
                                                        {/* TODO: port @endcan */}
                                                        {/* TODO: port @can */}
                                                        <Button variant="ghost" onClick={() => handleSendPasswordResetLink('{{ $u->uuid }}')} className=""><KeyRound className="size-4" />
                                                            {/* TODO: port @endcan */}
                                                            {/* TODO: port @if(! $u->hasVerifiedEmail()) */}
                                                            {/* TODO: port @can */}
                                                            <Button variant="ghost" onClick={() => handleResendVerification('{{ $u->uuid }}')} className=""><MailCheck className="size-4" />
                                                                {/* TODO: port @endcan */}
                                                                {/* TODO: port @endif */}
                                                                {/* TODO: port @can */}
                                                                {/* TODO: port @if($u->isSuspended()) */}
                                                                <Button variant="ghost" onClick={() => handleActivate('{{ $u->uuid }}')} className=""><UserCheck className="size-4" />
                                                                    {/* TODO: port @else */}
                                                                    <Button variant="ghost" onClick={() => confirmAction(() => handleSuspend('{{ $u->uuid }}'), '{{ __(\':name will no longer be able to sign in. Continue?\', [\'name\' => $u->name]) }}')} className=""><UserX className="size-4" />
                                                                        {/* TODO: port @endif */}
                                                                        {/* TODO: port @endcan */}
                                                                        {/* TODO: port @can */}
                                                                        <Button variant="destructive" onClick={() => confirmAction(() => handleDelete('{{ $u->uuid }}'), '{{ __(\'Are you sure you want to delete :name? The account can be restored later.\', [\'name\' => $u->name]) }}')} className=""><Trash2 className="size-4" />
                                                                            {/* TODO: port @endcan */}
                                                                            {/* TODO: port @endif */}
                                                                            </div>
                                                                            </div>
                                                                            {/* TODO: port @endif */}
                                                                            </div>
        </AppLayout>
    );
}