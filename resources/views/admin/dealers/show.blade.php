@extends('admin.layouts.app')

@section('title', 'View Dealer')

@section('content')
    <div class="w-full max-w-5xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-[#5D5CFF]">Dealer Details</p>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $dealer->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $dealer->email }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('admin.dealers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-[#5D5CFF] hover:text-[#5D5CFF]">
                    Back
                </a>
                <form action="{{ route('admin.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Delete this dealer permanently?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700 sm:w-auto">
                        <span class="material-symbols-outlined !text-[20px]">delete</span>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <section class="dealer-detail-section">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-950">Registration Information</h2>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <form action="{{ route('admin.dealers.status', $dealer) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select
                            name="dealer_status"
                            onchange="this.form.submit()"
                            class="dealer-status-select {{ $dealer->dealer_status === \App\Models\User::DEALER_STATUS_APPROVED ? 'is-approved' : 'is-pending' }}"
                            aria-label="Update dealer approval status"
                        >
                            @foreach ($dealerStatuses as $value => $label)
                                <option value="{{ $value }}" @selected(($dealer->dealer_status ?: \App\Models\User::DEALER_STATUS_PENDING) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
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
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="dealer-detail-card">
                    <span>Dealer Name</span>
                    <p>{{ $dealer->name }}</p>
                </div>
                <div class="dealer-detail-card">
                    <span>Email</span>
                    <p>{{ $dealer->email }}</p>
                </div>
                <div class="dealer-detail-card">
                    <span>Phone Number</span>
                    <p>{{ $dealer->phone ?: '-' }}</p>
                </div>
                <div class="dealer-detail-card">
                    <span>GST Number</span>
                    <p class="uppercase tracking-wide">{{ $dealer->gst_number ?: '-' }}</p>
                </div>
                <div class="dealer-detail-card">
                    <span>Approval Status</span>
                    <p class="{{ $dealer->dealer_status === \App\Models\User::DEALER_STATUS_APPROVED ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $dealerStatuses[$dealer->dealer_status ?: \App\Models\User::DEALER_STATUS_PENDING] ?? 'Pending' }}
                    </p>
                </div>
                <div class="dealer-detail-card">
                    <span>Account Status</span>
                    <p class="{{ $dealer->is_active ? 'text-blue-700' : 'text-red-700' }}">{{ $dealer->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
                <div class="dealer-detail-card">
                    <span>Registration Date</span>
                    <p>{{ $dealer->created_at ? $dealer->created_at->format('M d, Y h:i A') : '-' }}</p>
                </div>
                <div class="dealer-detail-card">
                    <span>Last Updated</span>
                    <p>{{ $dealer->updated_at ? $dealer->updated_at->format('M d, Y h:i A') : '-' }}</p>
                </div>
            </div>
        </section>
    </div>

    <style>
        .dealer-detail-section {
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background: white;
            padding: 1.5rem;
            box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.45);
        }

        @media (min-width: 640px) {
            .dealer-detail-section {
                padding: 1.75rem;
            }
        }

        .dealer-detail-card {
            border-radius: 1rem;
            border: 1px solid rgb(241 245 249);
            background: rgb(248 250 252);
            padding: 1rem;
        }

        .dealer-detail-card span {
            display: block;
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgb(148 163 184);
        }

        .dealer-detail-card p {
            margin-top: 0.5rem;
            overflow-wrap: anywhere;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgb(30 41 59);
        }

        .dealer-detail-card p.text-emerald-700 {
            color: rgb(21 128 61);
        }

        .dealer-detail-card p.text-amber-700 {
            color: rgb(180 83 9);
        }

        .dealer-detail-card p.text-blue-700 {
            color: rgb(29 78 216);
        }

        .dealer-detail-card p.text-red-700 {
            color: rgb(185 28 28);
        }

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
    </style>
@endsection
