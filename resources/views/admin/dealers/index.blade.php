@extends('admin.layouts.app')

@section('title', 'Dealers')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Dealers</p>
            <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Dealer Registrations</h1>
            <p class="mt-2 text-sm text-slate-500">Registered storefront dealers are listed separately from customers.</p>
        </div>

        <form method="GET" action="{{ route('admin.dealers.index') }}" class="flex w-full gap-2 sm:w-auto">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search dealers"
                class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none transition focus:border-[#5D5CFF] focus:ring-4 focus:ring-[#5D5CFF]/10 sm:w-72"
            >
            <button type="submit" class="rounded-lg bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                Search
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1280px] table-fixed divide-y divide-slate-200">
                <colgroup>
                    <col class="w-[15%]">
                    <col class="w-[20%]">
                    <col class="w-[12%]">
                    <col class="w-[15%]">
                    <col class="w-[11%]">
                    <col class="w-[12%]">
                    <col class="w-[10%]">
                    <col class="w-[7%]">
                </colgroup>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Dealer Name</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Phone Number</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">GST Number</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Registration Date</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Approval</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Account</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($dealers as $dealer)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $dealer->name }}</td>
                            <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $dealer->email }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $dealer->phone ?: '-' }}</td>
                            <td class="px-5 py-4 text-sm font-semibold uppercase tracking-wide text-slate-700">{{ $dealer->gst_number ?: '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ optional($dealer->created_at)->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.dealers.status', $dealer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="dealer_status"
                                        onchange="this.form.submit()"
                                        class="dealer-status-select {{ $dealer->dealer_status === \App\Models\User::DEALER_STATUS_APPROVED ? 'is-approved' : 'is-pending' }}"
                                        aria-label="Update dealer status"
                                    >
                                        @foreach ($dealerStatuses as $value => $label)
                                            <option value="{{ $value }}" @selected(($dealer->dealer_status ?: \App\Models\User::DEALER_STATUS_PENDING) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.dealers.status', $dealer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="is_active"
                                        onchange="this.form.submit()"
                                        class="dealer-status-select {{ $dealer->is_active ? 'is-active' : 'is-inactive' }}"
                                        aria-label="Update dealer account status"
                                    >
                                        <option value="1" @selected($dealer->is_active)>Active</option>
                                        <option value="0" @selected(! $dealer->is_active)>Inactive</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.dealers.show', $dealer) }}" class="dealer-action-button" title="View">
                                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Delete this dealer permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dealer-action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete">
                                            <span class="material-symbols-outlined !text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-slate-500">No dealers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $dealers->links() }}
    </div>

    <style>
        .dealer-status-select {
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

        .dealer-status-select.is-approved {
            border-color: rgb(187 247 208);
            background: rgb(240 253 244);
            color: rgb(21 128 61);
        }

        .dealer-status-select.is-pending {
            border-color: rgb(253 230 138);
            background: rgb(255 251 235);
            color: rgb(180 83 9);
        }

        .dealer-status-select.is-active {
            border-color: rgb(191 219 254);
            background: rgb(239 246 255);
            color: rgb(29 78 216);
        }

        .dealer-status-select.is-inactive {
            border-color: rgb(254 202 202);
            background: rgb(254 242 242);
            color: rgb(185 28 28);
        }

        .dealer-action-button {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            color: rgb(100 116 139);
            transition: all 150ms ease;
        }

        .dealer-action-button:hover {
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
        }
    </style>
@endsection
