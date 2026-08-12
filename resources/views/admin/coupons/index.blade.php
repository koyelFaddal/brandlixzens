@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Coupons</h1>
                <p class="mt-1 text-sm text-slate-500">Manage coupon codes, discount rules, product eligibility, and status.</p>
            </div>

            <a href="{{ route('admin.coupons.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                <span class="material-symbols-outlined !text-[20px]">add</span>
                Add Coupon
            </a>
        </div>

        @error('coupon')
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('admin.coupons.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-3 md:flex-row">
                <div class="relative w-full md:w-80">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                           placeholder="Search by name or code">
                </div>

                <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                    <option value="">All Status</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ $coupons->total() }} coupon{{ $coupons->total() === 1 ? '' : 's' }} found</span>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                    Search
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1180px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Coupon Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Coupon Code</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Discount</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Applicable To</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Validity</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Usage Limit</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Created Date</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($coupons as $coupon)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $coupon->coupon_name }}</p>
                                    @if ($coupon->description)
                                        <p class="mt-1 max-w-xs truncate text-xs text-slate-400">{{ $coupon->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-3 py-1 text-xs font-black tracking-widest text-slate-700">{{ $coupon->coupon_code }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-slate-800">{{ $coupon->discount_label }}</p>
                                    <p class="text-xs capitalize text-slate-400">{{ str_replace('_', ' ', $coupon->discount_type) }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-700">{{ $coupon->applicable_label }}</p>
                                    @if ($coupon->applicable_type === \App\Models\Coupon::APPLICABLE_SELECTED_PRODUCTS)
                                        <p class="text-xs text-slate-400">{{ $coupon->products_count }} product{{ $coupon->products_count === 1 ? '' : 's' }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold capitalize {{ $coupon->isActive() ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $coupon->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    <p>{{ $coupon->start_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-400">to {{ $coupon->end_date->format('M d, Y') }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    <p>Total: {{ $coupon->total_usage_limit ?? 'Unlimited' }}</p>
                                    <p class="text-xs text-slate-400">Per user: {{ $coupon->per_user_usage_limit ?? 'Unlimited' }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $coupon->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <form action="{{ route('admin.coupons.status', $coupon) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-button" title="{{ $coupon->isActive() ? 'Deactivate' : 'Activate' }}">
                                                <span class="material-symbols-outlined !text-[20px]">{{ $coupon->isActive() ? 'toggle_off' : 'toggle_on' }}</span>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="action-button" title="Edit">
                                            <span class="material-symbols-outlined !text-[20px]">edit</span>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Delete this coupon?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete">
                                                <span class="material-symbols-outlined !text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-sm text-slate-500">No coupons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($coupons->total())
                        Showing {{ $coupons->firstItem() }} to {{ $coupons->lastItem() }} of {{ $coupons->total() }} coupons
                    @else
                        No coupons to show
                    @endif
                </p>
                <div>
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .action-button {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            color: rgb(100 116 139);
            transition: all 150ms ease;
        }

        .action-button:hover {
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
        }
    </style>
@endsection
