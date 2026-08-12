@extends('admin.layouts.app')

@section('title', 'Order '.$order->order_number)

@section('content')
    <div class="w-full max-w-6xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ ! empty($isGiftOrderContext) ? route('admin.gift-orders.index') : route('admin.orders.index') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-primary">
                    <span class="material-symbols-outlined !text-[18px]">arrow_back</span>
                    Back to {{ ! empty($isGiftOrderContext) ? 'Gift Orders' : 'Orders' }}
                </a>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $order->order_number }}</h1>
                <p class="mt-1 text-sm text-slate-500">Placed on {{ $order->ordered_at?->format('M d, Y h:i A') }}</p>
            </div>

            <div class="w-full max-w-md rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:w-auto">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Current Status</span>
                    <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary">{{ $statuses[$order->status] ?? $order->status }}</span>
                </div>

                @if ($nextActions)
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3" data-order-status-form>
                        @csrf
                        @method('PATCH')
                        <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600 outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                            <option value="">Select next action</option>
                            @foreach ($nextActions as $value => $label)
                                <option value="{{ $value }}" @selected(old('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="{{ old('status') === \App\Models\Order::STATUS_REJECTED ? '' : 'hidden' }}" data-rejection-wrap>
                            <label for="rejection_reason" class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Reason for Rejection</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20" placeholder="Explain why this order is being rejected.">{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')
                                <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                            <span class="material-symbols-outlined !text-[20px]">sync</span>
                            Update Workflow
                        </button>
                    </form>
                @else
                    <p class="text-sm leading-6 text-slate-500">This order is in a final status. No further workflow updates are available.</p>
                @endif
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_360px]">
            <div class="space-y-5">
                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Order Items</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[680px] text-left">
                            <thead class="border-b border-slate-100 bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Product</th>
                                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Unit Price</th>
                                    <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-slate-400">Qty</th>
                                    <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Line Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-slate-900">{{ $item->product_name }}</p>
                                            <p class="mt-1 text-xs text-slate-400">{{ $item->product_sku ?: '-' }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-slate-600">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-4 text-right text-sm font-semibold text-slate-900">₹{{ number_format((float) $item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                @if ($order->giftOrder)
                    <section class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950">Gift Order Details</h2>
                                <p class="mt-1 text-sm text-slate-500">Gift card and message attached to this order.</p>
                            </div>
                            @if ($order->giftOrder->include_greeting_card)
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <a href="{{ route('admin.gift-orders.card', ['order' => $order, 'view' => 1]) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-xs font-bold uppercase tracking-widest text-slate-700 transition hover:border-primary hover:text-primary">
                                        <span class="material-symbols-outlined !text-[18px]">preview</span>
                                        View Card
                                    </a>
                                    <a href="{{ route('admin.gift-orders.card', $order) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-3 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-primary">
                                        <span class="material-symbols-outlined !text-[18px]">open_in_new</span>
                                        Open Card
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-xl bg-amber-50 p-4 text-sm leading-7 text-slate-700">
                                <p><strong class="text-slate-950">Gift Order:</strong> Yes</p>
                                <p><strong class="text-slate-950">Greeting Card:</strong> {{ $order->giftOrder->greeting_card_title ?: 'No card selected' }}</p>
                                <p><strong class="text-slate-950">Card Size:</strong> {{ $order->giftOrder->card_size }}</p>
                                <p><strong class="text-slate-950">Gift Order Status:</strong> {{ $statuses[$order->status] ?? $order->status }}</p>
                            </div>
                            <div class="rounded-xl border border-amber-100 bg-white p-4">
                                <h3 class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Greeting Message</h3>
                                <p class="whitespace-pre-wrap text-sm leading-7 text-slate-700">{{ $order->giftOrder->greeting_message ?: '-' }}</p>
                            </div>
                        </div>
                    </section>
                @endif

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Delivery Address</h2>
                    <div class="text-sm leading-7 text-slate-600">
                        <p class="font-semibold text-slate-900">{{ $order->delivery_name }}</p>
                        <p>{{ $order->address_line_1 }}</p>
                        @if ($order->address_line_2)
                            <p>{{ $order->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}</p>
                        <p>{{ $order->country }}</p>
                        <p>Phone: {{ $order->delivery_phone }}</p>
                        @if ($order->delivery_email)
                            <p>Email: {{ $order->delivery_email }}</p>
                        @endif
                        @if ($order->delivery_notes)
                            <p class="mt-3 rounded-xl bg-slate-50 p-3">{{ $order->delivery_notes }}</p>
                        @endif
                    </div>
                </section>

                @if ($order->status === \App\Models\Order::STATUS_REJECTED && $order->rejection_reason)
                    <section class="rounded-2xl border border-red-100 bg-red-50 p-5 shadow-sm">
                        <h2 class="mb-3 font-['Space_Grotesk'] text-xl font-bold text-red-900">Rejection Reason</h2>
                        <p class="text-sm leading-7 text-red-800">{{ $order->rejection_reason }}</p>
                    </section>
                @endif
            </div>

            <aside class="space-y-5">
                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Delivery Schedule</h2>
                    <form action="{{ route('admin.orders.delivery-schedule', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="expected_delivery_date" class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Expected Delivery Date</label>
                            <input id="expected_delivery_date" type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date', $order->expected_delivery_date?->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                            @error('expected_delivery_date')
                                <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="expected_delivery_time" class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-400">Expected Delivery Time</label>
                            <input id="expected_delivery_time" type="time" name="expected_delivery_time" value="{{ old('expected_delivery_time', $order->expected_delivery_time ? substr((string) $order->expected_delivery_time, 0, 5) : '') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                            @error('expected_delivery_time')
                                <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-primary/20 bg-primary/10 px-5 py-3 text-sm font-bold uppercase tracking-widest text-primary transition hover:bg-primary hover:text-white">
                            <span class="material-symbols-outlined !text-[20px]">event_available</span>
                            Save Schedule
                        </button>
                    </form>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Customer</h2>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">{{ $order->customer_name }}</p>
                        <p>{{ $order->customer_email }}</p>
                        <p>{{ $order->customer_phone ?: '-' }}</p>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Payment & Totals</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4 text-slate-600">
                            <span>Payment Method</span>
                            <strong class="text-slate-900">{{ $paymentMethods[$order->payment_method] ?? $order->payment_method }}</strong>
                        </div>
                        <div class="flex justify-between gap-4 text-slate-600">
                            <span>Subtotal</span>
                            <strong class="text-slate-900">₹{{ number_format((float) $order->subtotal, 2) }}</strong>
                        </div>
                        <div class="flex justify-between gap-4 text-slate-600">
                            <span>Delivery Charge</span>
                            <strong class="text-slate-900">{{ (float) $order->delivery_charge > 0 ? '₹'.number_format((float) $order->delivery_charge, 2) : 'FREE' }}</strong>
                        </div>
                        @if ((float) $order->coupon_discount_amount > 0)
                            <div class="flex justify-between gap-4 text-emerald-700">
                                <span>Coupon{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</span>
                                <strong>-â‚¹{{ number_format((float) $order->coupon_discount_amount, 2) }}</strong>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4 border-t border-slate-100 pt-4 text-lg font-bold text-slate-950">
                            <span>Grand Total</span>
                            <span>₹{{ number_format((float) $order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-order-status-form]').forEach((form) => {
            const select = form.querySelector('select[name="status"]');
            const rejectionWrap = form.querySelector('[data-rejection-wrap]');

            if (!select || !rejectionWrap) {
                return;
            }

            const syncReason = () => {
                rejectionWrap.classList.toggle('hidden', select.value !== '{{ \App\Models\Order::STATUS_REJECTED }}');
            };

            select.addEventListener('change', syncReason);
            syncReason();
        });
    </script>
@endsection
