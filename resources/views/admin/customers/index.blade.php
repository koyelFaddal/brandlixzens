@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Customers</p>
            <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Customers / User List</h1>
            <p class="mt-2 text-sm text-slate-500">Registered storefront customers are listed here.</p>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex w-full gap-2 sm:w-auto">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search customers"
                class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none transition focus:border-[#5D5CFF] focus:ring-4 focus:ring-[#5D5CFF]/10 sm:w-72"
            >
            <button type="submit" class="rounded-lg bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                Search
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1080px] table-fixed divide-y divide-slate-200">
                <colgroup>
                    <col class="w-[20%]">
                    <col class="w-[26%]">
                    <col class="w-[16%]">
                    <col class="w-[14%]">
                    <col class="w-[14%]">
                    <col class="w-[10%]">
                </colgroup>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Phone Number</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Registration Date</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($customers as $customer)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $customer->name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600 break-words">{{ $customer->email }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $customer->phone ?: '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ optional($customer->created_at)->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="is_active"
                                        onchange="this.form.submit()"
                                        class="customer-status-select {{ $customer->is_active ? 'is-active' : 'is-inactive' }}"
                                        aria-label="Update customer status"
                                    >
                                        <option value="1" @selected($customer->is_active)>Active</option>
                                        <option value="0" @selected(! $customer->is_active)>Inactive</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="customer-action-button" title="View">
                                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete this customer permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="customer-action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete">
                                            <span class="material-symbols-outlined !text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $customers->links() }}
    </div>

    <style>
        .customer-status-select {
            height: 2.25rem;
            min-width: 7.5rem;
            border-radius: 999px;
            border: 1px solid rgb(226 232 240);
            padding: 0 2rem 0 0.85rem;
            font-size: 0.8125rem;
            font-weight: 700;
            outline: none;
            transition: all 150ms ease;
        }

        .customer-status-select.is-active {
            border-color: rgb(187 247 208);
            background: rgb(240 253 244);
            color: rgb(21 128 61);
        }

        .customer-status-select.is-inactive {
            border-color: rgb(254 202 202);
            background: rgb(254 242 242);
            color: rgb(185 28 28);
        }

        .customer-action-button {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            color: rgb(100 116 139);
            transition: all 150ms ease;
        }

        .customer-action-button:hover {
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
        }
    </style>
@endsection
