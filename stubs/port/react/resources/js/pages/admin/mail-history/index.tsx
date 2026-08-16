import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge, Button, Dialog, DropdownMenuItem, Input, Label, Select } from '@/components/ui/*'; // split per shadcn convention
import { Eye, MagnifyingGlass } from 'lucide-react';
import { useState } from 'react';

type Props = {
    perPage?: any;
    search?: any;
    showDetail?: any;
    status?: any;
    viewingUuid?: any;
};

export default function AdminMailHistoryIndex(props: Props) {
    const [perPage, setPerPage] = useState<any>(props.perPage);
    const [search, setSearch] = useState<any>(props.search);
    const [showDetail, setShowDetail] = useState<any>(props.showDetail);
    const [status, setStatus] = useState<any>(props.status);
    const [viewingUuid, setViewingUuid] = useState<any>(props.viewingUuid);
    function handleClearFilters(...args: any[]) {
        router.post(route('port.clearFilters'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleCloseDetail(...args: any[]) {
        router.post(route('port.closeDetail'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }
    function handleView(...args: any[]) {
        router.post(route('port.view'), { args }, { preserveScroll: true }); // TODO: real route/payload
    }

    return (
        <AppLayout>
            <Head title="AdminMailHistoryIndex" />
<div>
{/* TODO: port @php block */}
$statusColor = fn (string $status) => match ($status) {
'Delivered', 'Opened', 'Clicked' => 'green',
'Sent' => 'blue',
'Sending' => 'zinc',
'Bounced', 'Complained', 'Failed' => 'red',
default => 'zinc',
};
$mh = \App\Livewire\Admin\MailHistory\Index::class;
{/* TODO: port @endphp */}
{/*Filters Toolbar */}
<div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
<div className="flex-1">
<div className="grid gap-2">
    <Input id="field" type="search" />
</div>
</div>
<div className="grid grid-cols-1 gap-3 sm:flex sm:items-center">
<Select value={status} onValueChange={setStatus}>
    <SelectTrigger><SelectValue /></SelectTrigger>
    <SelectContent>
        <SelectItem value="">
            {/* TODO: port @foreach */}
            <SelectItem value="{{ $s }}">
                {/* TODO: port @endforeach */}
            </SelectContent>
        </Select>
        </div>
        </div>
        {/*Summary + Per-page */}
        <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
        <span>Showing <strong className="text-zinc-900 dark:text-white">{mails.total()}</strong> messages</span>
        {/* TODO: port @if($search || $status) */}
        <Button variant="ghost" onClick={() => handleClearFilters()}>
            {/* TODO: port @endif */}
            </div>
            <div className="flex items-center gap-2">
            <label className="text-sm text-zinc-500 dark:text-zinc-400">Per page</label>
            <Select value={perPage} onValueChange={setPerPage}>
                <SelectTrigger className="!w-24"><SelectValue /></SelectTrigger>
                <SelectContent>
                    <SelectItem value="10">
                        <SelectItem value="25">
                            <SelectItem value="50">
                                <SelectItem value="100">
                                </SelectContent>
                            </Select>
                            </div>
                            </div>
                            {/*Table */}
                            <div className="flow-root">
                            <div className="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div className="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <div className="overflow-hidden outline-1 -outline-offset-1 outline-zinc-200 dark:outline-zinc-700 sm:rounded-lg">
                            <table className="relative min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead className="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                            <th scope="col" className="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100 sm:pl-6">Subject &amp; Recipient</th>
                            <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">Status</th>
                            <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-zinc-900 dark:text-zinc-100">Sent</th>
                            <th scope="col" className="py-3.5 pr-4 pl-3 sm:pr-6"><span className="sr-only">Actions</span></th>
                            </tr>
                            </thead>
                            <tbody className="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                            {/* TODO: port @forelse */}
                            <tr className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td className="py-4 pr-3 pl-4 text-sm sm:pl-6">
                            <div className="font-medium text-zinc-900 dark:text-white">{mh::header(mail, 'Subject') ?? '(no subject)'}</div>
                            <div className="text-zinc-500 dark:text-zinc-400">{mh::header(mail, 'To') ?? '—'}</div>
                            </td>
                            <td className="px-3 py-4 text-sm">
                            <Badge>
                                </td>
                                <td className="px-3 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                <div>{mail.created_at.format('M d, Y H:i')}</div>
                                <div className="text-xs text-zinc-400 dark:text-zinc-500">{mail.created_at.diffForHumans()}</div>
                                </td>
                                <td className="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                {/* TODO: port <x-row-actions> */}
                                <DropdownMenuItem onClick={() => handleView('{{ $mail->uuid }}')}><Eye className="mr-2 size-4" />
                                    </td>
                                    </tr>
                                    {/* TODO: port @empty */}
                                    <tr>
                                    <td colspan="4" className="py-12 text-center">
                                    {/* TODO: port <x-empty-state> */}
                                    title="No mail history"
                                    description="No emails have been sent yet, or none match your filters."
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
                                    {mails.links()}
                                    </div>
                                    {/*Detail Modal */}
                                    <Dialog open={showDetail} onOpenChange={setShowDetail}>
                                        <DialogContent className="md:max-w-2xl">
                                            {/* TODO: port @if($viewing) */}
                                            <div className="space-y-5">
                                            <div>
                                            <h2 className="text-xl font-semibold">
                                                <p className="text-sm text-muted-foreground mt-1">
                                                    <Badge>
                                                    </p>
                                                    </div>
                                                    <dl className="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-2">
                                                    <div>
                                                    <dt className="font-medium text-zinc-500 dark:text-zinc-400">To</dt>
                                                    <dd className="text-zinc-900 dark:text-white">{mh::header(viewing, 'To') ?? '—'}</dd>
                                                    </div>
                                                    <div>
                                                    <dt className="font-medium text-zinc-500 dark:text-zinc-400">From</dt>
                                                    <dd className="text-zinc-900 dark:text-white">{mh::header(viewing, 'From') ?? '—'}</dd>
                                                    </div>
                                                    <div>
                                                    <dt className="font-medium text-zinc-500 dark:text-zinc-400">Sent</dt>
                                                    <dd className="text-zinc-900 dark:text-white">{viewing.created_at.format('M d, Y H:i:s')}</dd>
                                                    </div>
                                                    <div>
                                                    <dt className="font-medium text-zinc-500 dark:text-zinc-400">Reference</dt>
                                                    <dd className="font-mono text-xs text-zinc-700 dark:text-zinc-300">{viewing.uuid}</dd>
                                                    </div>
                                                    </dl>
                                                    {/*Event timeline */}
                                                    <div>
                                                    <h2 className="text-lg font-semibold mb-3">
                                                        {/* TODO: port @php block */}
                                                        {/* TODO: port @if($timeline->isNotEmpty()) */}
                                                        <ol className="relative ml-1 space-y-4 border-l border-zinc-200 pl-5 dark:border-zinc-700">
                                                        {/* TODO: port @foreach */}
                                                        {/* TODO: port @php block */}
                                                        $dot = match ($event->type) {
                                                        'delivered' => 'bg-blue-500',
                                                        'opened' => 'bg-green-500',
                                                        'clicked' => 'bg-indigo-500',
                                                        'bounced', 'complained', 'failed' => 'bg-red-500',
                                                        default => 'bg-zinc-400',
                                                        };
                                                        {/* TODO: port @endphp */}
                                                        <li className="relative">
                                                        <span className="absolute top-1 -left-[1.65rem] h-2.5 w-2.5 rounded-full ring-4 ring-white dark:ring-zinc-900 {dot}"></span>
                                                        <div className="flex items-baseline justify-between gap-3">
                                                        <span className="font-medium text-zinc-900 capitalize dark:text-white">{event.type}</span>
                                                        <span className="text-xs text-zinc-500 dark:text-zinc-400">{\Illuminate\Support\Carbon::parse(event.occurred_at).format('M d, Y H:i:s')}</span>
                                                        </div>
                                                        </li>
                                                        {/* TODO: port @endforeach */}
                                                        </ol>
                                                        {/* TODO: port @else */}
                                                        <p className="text-sm text-zinc-500 dark:text-zinc-400">No opens or clicks recorded yet. (Delivered/bounced tracking needs a webhook-capable mail provider.)</p>
                                                        {/* TODO: port @endif */}
                                                        </div>
                                                        {/*Attachments */}
                                                        {/* TODO: port @php block */}
                                                        {/* TODO: port @if(! empty($attachments)) */}
                                                        <div>
                                                        <h2 className="text-lg font-semibold mb-2">
                                                            <ul className="flex flex-wrap gap-2">
                                                            {/* TODO: port @foreach */}
                                                            <li className="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 px-2.5 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                                            {/* TODO: port @svg('lucide-paperclip', 'h-3.5 w-3.5') */}
                                                            <span className="font-mono">{attachment}</span>
                                                            </li>
                                                            {/* TODO: port @endforeach */}
                                                            </ul>
                                                            </div>
                                                            {/* TODO: port @endif */}
                                                            {/*Body */}
                                                            {/* TODO: port @php block */}
                                                            $bodyHtml = $viewing->content['html'] ?? null;
                                                            $bodyText = $viewing->content['text'] ?? null;
                                                            $bodyRaw = (! $bodyHtml && ! $bodyText) ? $viewing->body : null;
                                                            $defaultTab = $bodyHtml ? 'preview' : ($bodyText ? 'text' : 'raw');
                                                            {/* TODO: port @endphp */}
                                                            {/* TODO: port @if($bodyHtml || $bodyText || $bodyRaw) */}
                                                            <div x-data="{ bodyTab: '{defaultTab}' }">
                                                            <div className="mb-2 flex items-center gap-1">
                                                            <h2 className="text-lg font-semibold mr-2">
                                                                {/* TODO: port @if($bodyHtml) */}
                                                                <button type="button" :class="bodyTab === 'preview' ? 'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" className="cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium">Preview</button>
                                                                <button type="button" :class="bodyTab === 'source' ? 'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" className="cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium">HTML source</button>
                                                                {/* TODO: port @endif */}
                                                                {/* TODO: port @if($bodyText) */}
                                                                <button type="button" :class="bodyTab === 'text' ? 'bg-zinc-200 text-zinc-900 dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'" className="cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium">Text</button>
                                                                {/* TODO: port @endif */}
                                                                </div>
                                                                {/* TODO: port @if($bodyHtml) */}
                                                                <div x-show="bodyTab === 'preview'">
                                                                <iframe
                                                                sandbox=""
                                                                title="Email preview"
                                                                srcdoc="{bodyHtml}"
                                                                className="h-96 w-full rounded-md border border-zinc-200 bg-white dark:border-zinc-700"
                                                                ></iframe>
                                                                </div>
                                                                <div x-show="bodyTab === 'source'" x-cloak>
                                                                <pre className="max-h-96 overflow-auto rounded-md border border-zinc-200 bg-zinc-50 p-3 text-xs text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">{bodyHtml}</pre>
                                                                </div>
                                                                {/* TODO: port @endif */}
                                                                {/* TODO: port @if($bodyText) */}
                                                                <div x-show="bodyTab === 'text'" @if ($bodyHtml) x-cloak @endif>
                                                                <pre className="max-h-96 overflow-auto rounded-md border border-zinc-200 bg-white p-3 text-sm whitespace-pre-wrap break-words font-sans text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">{bodyText}</pre>
                                                                </div>
                                                                {/* TODO: port @endif */}
                                                                {/* TODO: port @if($bodyRaw) */}
                                                                <pre className="max-h-96 overflow-auto rounded-md border border-zinc-200 bg-white p-3 text-xs whitespace-pre-wrap break-words text-zinc-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">{\Illuminate\Support\Str::limit(bodyRaw, 8000)}</pre>
                                                                {/* TODO: port @endif */}
                                                                </div>
                                                                {/* TODO: port @endif */}
                                                                </div>
                                                                <div className="mt-6 flex justify-end">
                                                                <Button variant="ghost" onClick={() => handleCloseDetail()}>
                                                                    </div>
                                                                    {/* TODO: port @endif */}
                                                                </DialogContent>
                                                            </Dialog>
                                                            </div>
        </AppLayout>
    );
}