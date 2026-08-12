@extends('admin.layouts.app')

@section('title', 'View Customer')

@section('content')
    <div class="w-full max-w-5xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-[#5D5CFF]">Customer Details</p>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $customer->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $customer->email }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-[#5D5CFF] hover:text-[#5D5CFF]">
                    Back
                </a>
                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete this customer permanently?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-red-600/20 transition hover:bg-red-700 sm:w-auto">
                        <span class="material-symbols-outlined !text-[20px]">delete</span>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <section class="customer-detail-section">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-['Space_Grotesk'] text-lg font-bold text-slate-950">Account Information</h2>
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
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="customer-detail-card">
                    <span>Name</span>
                    <p>{{ $customer->name }}</p>
                </div>
                <div class="customer-detail-card">
                    <span>Email</span>
                    <p>{{ $customer->email }}</p>
                </div>
                <div class="customer-detail-card">
                    <span>Phone Number</span>
                    <p>{{ $customer->phone ?: '-' }}</p>
                </div>
                <div class="customer-detail-card">
                    <span>Status</span>
                    <p class="{{ $customer->is_active ? 'text-emerald-700' : 'text-red-700' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
                <div class="customer-detail-card">
                    <span>Gender</span>
                    <p>{{ $customer->gender ? ucfirst($customer->gender) : '-' }}</p>
                </div>
                <div class="customer-detail-card">
                    <span>Registration Date</span>
                    <p>{{ $customer->created_at ? $customer->created_at->format('M d, Y h:i A') : '-' }}</p>
                </div>
                <div class="customer-detail-card md:col-span-2">
                    <span>Contact Address</span>
                    <p>{{ $customer->contact_address ?: '-' }}</p>
                </div>
            </div>
        </section>
    </div>

    <style>
        .customer-detail-section {
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background: white;
            padding: 1.5rem;
            box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.45);
        }

        @media (min-width: 640px) {
            .customer-detail-section {
                padding: 1.75rem;
            }
        }

        .customer-detail-card {
            border-radius: 1rem;
            border: 1px solid rgb(241 245 249);
            background: rgb(248 250 252);
            padding: 1rem;
        }

        .customer-detail-card span {
            display: block;
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgb(148 163 184);
        }

        .customer-detail-card p {
            margin-top: 0.5rem;
            overflow-wrap: anywhere;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgb(30 41 59);
        }

        .customer-detail-card p.text-emerald-700 {
            color: rgb(21 128 61);
        }

        .customer-detail-card p.text-red-700 {
            color: rgb(185 28 28);
        }

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
    </style>
@endsection
