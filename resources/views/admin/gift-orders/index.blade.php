@extends('admin.layouts.app')

@section('title', 'Gift Orders')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Gift Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Review orders placed from the gift flow, greeting cards, messages, and fulfilment status.</p>
        </div>

        <form action="{{ route('admin.gift-orders.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search order, customer, card, or message">
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
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order Number</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Customer</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order Date</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Greeting Card</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Greeting Message</th>
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
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $statuses[$order->status] ?? $order->status }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $order->giftOrder?->greeting_card_title ?: 'No card' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($order->giftOrder?->greeting_message ?: '-', 70) }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        @if ($order->giftOrder?->include_greeting_card)
                                            <a href="{{ route('admin.gift-orders.card', ['order' => $order, 'view' => 1]) }}" class="action-button" title="View Greeting Card" target="_blank" rel="noopener">
                                                <span class="material-symbols-outlined !text-[20px]">preview</span>
                                            </a>
                                            <a href="{{ route('admin.gift-orders.card', $order) }}" class="action-button" title="Open Greeting Card" target="_blank" rel="noopener">
                                                <span class="material-symbols-outlined !text-[20px]">open_in_new</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.gift-orders.show', $order) }}" class="action-button" title="View">
                                            <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">No gift orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($orders->total())
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} gift orders
                    @else
                        No gift orders to show
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
