@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Review customer orders, totals, payment method, delivery charges, and current fulfilment status.</p>
        </div>

        <form action="{{ route('admin.orders.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search order ID, customer, email, or phone">
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600 outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                    Filter
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order ID</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Customer Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order Date</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Amount</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Payment Method</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Delivery Charge</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order Status</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $order)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $order->order_number }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $order->items_count }} item{{ $order->items_count === 1 ? '' : 's' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $order->customer_name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $order->customer_email }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $order->ordered_at?->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800">₹{{ number_format((float) $order->grand_total, 2) }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $paymentMethods[$order->payment_method] ?? $order->payment_method }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ (float) $order->delivery_charge > 0 ? '₹'.number_format((float) $order->delivery_charge, 2) : 'FREE' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $statuses[$order->status] ?? $order->status }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="action-button" title="View">
                                            <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($orders->total())
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
                    @else
                        No orders to show
                    @endif
                </p>
                <div>{{ $orders->links() }}</div>
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
