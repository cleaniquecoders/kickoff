<div>
    <div class="flex justify-end">
        {{ $roles->links() }}
    </div>
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden outline-1 -outline-offset-1 outline-white/10 sm:rounded-lg">
                    <table class="relative min-w-full divide-y divide-white/15">
                        <thead class="bg-brand-600/75">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-200 sm:pl-6">Role
                                </th>
                                <th scope="col" class="py-3.5 pr-4 pl-3 sm:pr-6">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-brand-600/50">
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-white sm:pl-6">
                                        {{ $role->display_name }}</td>
                                    <td class="py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                        <a href="{{ route('security.access-control.show', ['uuid' => $role->uuid]) }}"
                                            class="text-slate-400 hover:text-slate-300">Edit<span
                                                class="sr-only">Edit</span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        {{ $roles->links() }}
    </div>
</div>
