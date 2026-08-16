<script lang="ts">
import AppLayout from '@/layouts/AppLayout.svelte';
import { Link, router, useForm } from '@inertiajs/svelte';

// let props = $props(); // TODO: declare Inertia props from controller
// const form = useForm({ /* TODO: fields from wire:model bindings */ });
// let local = $state({});        // TODO: UI-only state (flyouts, selection)
// let total = $derived(0);       // TODO: values Livewire computed server-side
</script>

<svelte:head><title>Index</title></svelte:head>

<AppLayout>

<!--
TODO (6):
 - @can: authorization check — move to controller-provided `can` prop
 - @php: inline PHP — move to computed/derived value
 - deeply nested loops (6) — verify generated each-blocks against blade source
 - x-flyout blade component — port to kit equivalent or drop
 - Alpine $dispatch/$wire usage — port to Inertia router visit or emitted component event
 - verify blade echos against server-provided Inertia props (routes, translations)
-->

<div>
    
    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-xs text-zinc-500 dark:text-zinc-400">{__('Total Users')}</div>
            <div class="mt-1 text-2xl font-bold">{$this->stats['total']}</div>
        </div>
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700/50 dark:bg-green-900/20">
            <div class="text-xs text-green-700 dark:text-green-300">{__('Active')}</div>
            <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{$this->stats['active']}</div>
        </div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700/50 dark:bg-amber-900/20">
            <div class="text-xs text-amber-700 dark:text-amber-300">{__('Suspended')}</div>
            <div class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{$this->stats['suspended']}</div>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700/50 dark:bg-red-900/20">
            <div class="text-xs text-red-700 dark:text-red-300">{__('Deleted')}</div>
            <div class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{$this->stats['deleted']}</div>
        </div>
    </div>

    
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <Input bind:value={search} icon="magnifying-glass"
                placeholder="{__('Search by name or email...')}" clearable />
        </div>
        <div class="flex gap-3">
            <Select bind:value={roleFilter} class="sm:w-44">
                <SelectItem value="">{__('All Roles')}</SelectItem>

                    <SelectItem value="{$role->name}">{$role->display_name}</SelectItem>

            </Select>
            <Select bind:value={statusFilter} class="sm:w-44">
                <SelectItem value="">{__('All Statuses')}</SelectItem>

                    <SelectItem value="{$status->value}">{$status->label()}</SelectItem>

            </Select>
        </div>
    </div>

        <div
            class="mb-4 flex flex-col gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-3 sm:flex-row sm:items-center dark:border-zinc-700 dark:bg-zinc-800/50">
            <Text class="font-medium">
                {trans_choice(':count user selected|:count users selected', count($selected), ['count' => count($selected)])}
            </Text>
            <div class="flex-1"></div>
            <div class="flex flex-wrap items-center gap-2">

                    <Select bind:value={bulkRole} size="sm" class="w-40">
                        <SelectItem value="">{__('Assign role...')}</SelectItem>

                            <SelectItem value="{$role->name}">{$role->display_name}</SelectItem>

                    </Select>
                    <Button size="sm" onclick={bulkAssignRole} class="cursor-pointer">
                        {__('Apply')}
                    </Button>

                    <Button size="sm" variant="danger" icon="trash-2" onclick={bulkDelete}
                        data-confirm="{__(&#039;Delete :count selected user(s)? Protected accounts will be skipped. Deleted accounts can be restored later.&#039;, [&#039;count&#039; =&gt; count($selected)])}" /* TODO: wire confirm() into click handler */
                        class="cursor-pointer">
                        {__('Delete')}
                    </Button>

                <Button size="sm" variant="ghost" onclick={clearSelection} class="cursor-pointer">
                    {__('Clear')}
                </Button>
            </div>
        </div>


    
    <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    <th class="w-10 px-4 py-3">
                        <Checkbox bind:value={selectPage} />
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {__('User')}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {__('Roles')}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {__('Status')}</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-700 md:table-cell dark:text-zinc-300">
                        {__('Joined')}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                        {__('Actions')}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">

                    <tr /* TODO: wire:key — use a real each-block key */
                        onclick={openDetail('{$user->uuid}')}
                        class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                        <td class="px-4 py-4" @click.stop>
                            <Checkbox bind:value={selected} value="{$user->uuid}" />
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                        {$user->initials()}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate font-medium text-zinc-900 dark:text-zinc-100">{$user->name}
                                    </div>
                                    <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{$user->email}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">

                                    <Badge size="sm"
                                        color="{$role->name === 'superadmin' ? 'red' : ($role->name === 'administrator' ? 'amber' : 'zinc')}">
                                        {$role->display_name ?? $role->name}
                                    </Badge>

                                    <span class="text-sm italic text-zinc-400 dark:text-zinc-500">{__('No roles')}</span>

                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <Badge size="sm" color="{$user->status()->badgeColor()}">
                                {$user->status()->label()}
                            </Badge>
                        </td>
                        <td class="hidden px-4 py-4 text-sm text-zinc-500 md:table-cell dark:text-zinc-400">
                            {$user->created_at->format('M d, Y')}
                        </td>
                        <td class="px-4 py-4 text-right" @click.stop>
                            <DropdownMenu>
                                <Button variant="ghost" size="sm" icon="ellipsis" class="cursor-pointer" />
                                <DropdownMenuContent>

                                            <DropdownMenuItem icon="rotate-ccw" onclick={restore('{$user->uuid}')}>
                                                {__('Restore')}
                                            </DropdownMenuItem>

                                            <DropdownMenuItem icon="pencil"
                                                /* TODO: alpine handler */>
                                                {__('Edit')}
                                            </DropdownMenuItem>

                                            <DropdownMenuItem icon="shield-check"
                                                /* TODO: alpine handler */>
                                                {__('Manage Access')}
                                            </DropdownMenuItem>

                                            <DropdownMenuItem icon="user-check" href={route('impersonate', $user->id)}>
                                                {__('Impersonate')}
                                            </DropdownMenuItem>

                                            <DropdownMenuItem icon="key-round"
                                                onclick={sendPasswordResetLink('{$user->uuid}')}>
                                                {__('Send Password Reset')}
                                            </DropdownMenuItem>

                                                <DropdownMenuItem icon="mail-check"
                                                    onclick={resendVerification('{$user->uuid}')}>
                                                    {__('Resend Verification')}
                                                </DropdownMenuItem>

                                            <DropdownMenuSeparator />

                                                <DropdownMenuItem icon="user-check" onclick={activate('{$user->uuid}')}>
                                                    {__('Activate')}
                                                </DropdownMenuItem>

                                                <DropdownMenuItem icon="user-x" onclick={suspend('{$user->uuid}')}
                                                    data-confirm="{__(&#039;:name will no longer be able to sign in. Continue?&#039;, [&#039;name&#039; =&gt; $user-&gt;name])}" /* TODO: wire confirm() into click handler */>
                                                    {__('Suspend')}
                                                </DropdownMenuItem>

                                            <DropdownMenuItem icon="trash-2" variant="danger"
                                                onclick={delete('{$user->uuid}')}
                                                data-confirm="{__(&#039;Are you sure you want to delete :name? The account can be restored later.&#039;, [&#039;name&#039; =&gt; $user-&gt;name])}" /* TODO: wire confirm() into click handler */>
                                                {__('Delete')}
                                            </DropdownMenuItem>

                                </DropdownMenuContent>
                            </DropdownMenu>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            {__('No users found.')}
                        </td>
                    </tr>

            </tbody>
        </table>
    </div>

    
    <div class="mt-4">
        {$this->users->links()}
    </div>

    
    <!-- TODO: x-flyout -->

            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <span class="text-lg font-semibold text-zinc-600 dark:text-zinc-300">{$u->initials()}</span>
                    </div>
                    <div class="min-w-0">
                        <Heading size="lg">{$u->name}</Heading>
                        <Subheading>{$u->email}</Subheading>
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">{__('Status')}</dt>
                        <dd class="mt-1"><Badge size="sm" color="{$u->status()->badgeColor()}">{$u->status()->label()}</Badge></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">{__('Email verified')}</dt>
                        <dd class="mt-1 text-zinc-900 dark:text-zinc-100">{$u->hasVerifiedEmail() ? __('Yes') : __('No')}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">{__('Roles')}</dt>
                        <dd class="mt-1 flex flex-wrap gap-1">

                                <Badge size="sm" color="{$role->name === 'superadmin' ? 'red' : ($role->name === 'administrator' ? 'amber' : 'zinc')}">{$role->display_name ?? $role->name}</Badge>

                                <span class="italic text-zinc-400 dark:text-zinc-500">{__('No roles')}</span>

                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-zinc-500 dark:text-zinc-400">{__('Joined')}</dt>
                        <dd class="mt-1 text-zinc-900 dark:text-zinc-100">{$u->created_at->format('M d, Y')}</dd>
                    </div>
                </dl>

                <Separator variant="subtle" />

                <div class="flex flex-wrap gap-2">

                            <Button size="sm" icon="rotate-ccw" onclick={restore('{$u->uuid}')} class="cursor-pointer">{__('Restore')}</Button>

                            <Button size="sm" icon="pencil" class="cursor-pointer"
                                /* TODO: alpine handler */>{__('Edit')}</Button>

                            <Button size="sm" variant="ghost" icon="shield-check" class="cursor-pointer"
                                /* TODO: alpine handler */>{__('Manage Access')}</Button>

                            <Button size="sm" variant="ghost" icon="key-round" onclick={sendPasswordResetLink('{$u->uuid}')} class="cursor-pointer">{__('Send Password Reset')}</Button>

                                <Button size="sm" variant="ghost" icon="mail-check" onclick={resendVerification('{$u->uuid}')} class="cursor-pointer">{__('Resend Verification')}</Button>

                                <Button size="sm" variant="ghost" icon="user-check" onclick={activate('{$u->uuid}')} class="cursor-pointer">{__('Activate')}</Button>

                                <Button size="sm" variant="ghost" icon="user-x" onclick={suspend('{$u->uuid}')}
                                    data-confirm="{__(&#039;:name will no longer be able to sign in. Continue?&#039;, [&#039;name&#039; =&gt; $u-&gt;name])}" /* TODO: wire confirm() into click handler */
                                    class="cursor-pointer">{__('Suspend')}</Button>

                            <Button size="sm" variant="danger" icon="trash-2" onclick={delete('{$u->uuid}')}
                                data-confirm="{__(&#039;Are you sure you want to delete :name? The account can be restored later.&#039;, [&#039;name&#039; =&gt; $u-&gt;name])}" /* TODO: wire confirm() into click handler */
                                class="cursor-pointer">{__('Delete')}</Button>

                </div>
            </div>

    
</div>
</AppLayout>