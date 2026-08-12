@extends('admin.layouts.app')

@section('title', 'Bulk Orders')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Bulk Orders</p>
            <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Bulk Order Requests</h1>
            <p class="mt-2 text-sm text-slate-500">Review single-product bulk inquiries from product cards and product detail pages.</p>
        </div>

        @error('bulk_order')
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        @error('reject_reason')
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('admin.bulk-orders.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search product, name, email, or phone">
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ $bulkOrders->total() }} request{{ $bulkOrders->total() === 1 ? '' : 's' }} found</span>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                    Search
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1220px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Product</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Customer Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Phone</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Quantity</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Created Date</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bulkOrders as $bulkOrder)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($bulkOrder->product_image)
                                            <img src="{{ asset('storage/'.$bulkOrder->product_image) }}" loading="lazy" alt="{{ $bulkOrder->product_name }}" class="h-14 w-20 rounded-xl border border-slate-100 object-cover">
                                        @else
                                            <div class="flex h-14 w-20 items-center justify-center rounded-xl border border-dashed border-slate-200 text-slate-300">
                                                <span class="material-symbols-outlined">image</span>
                                            </div>
                                        @endif
                                        <span class="font-semibold text-slate-900">{{ $bulkOrder->product_name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $bulkOrder->full_name }}</td>
                                <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $bulkOrder->email }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $bulkOrder->phone }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ $bulkOrder->quantity }}</td>
                                <td class="px-5 py-4">
                                    <span class="bulk-order-status bulk-order-status-{{ $bulkOrder->status }}">{{ $bulkOrder->status_label }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $bulkOrder->created_at?->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.bulk-orders.show', $bulkOrder) }}" class="bulk-order-action is-view" title="View">
                                            <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                        </a>
                                        <form action="{{ route('admin.bulk-orders.approve', $bulkOrder) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bulk-order-action is-approve" title="{{ $bulkOrder->canApprove() ? 'Approve' : 'Only pending requests can be approved' }}" @disabled(! $bulkOrder->canApprove())>
                                                <span class="material-symbols-outlined !text-[20px]">check</span>
                                            </button>
                                        </form>
                                        <button type="button" class="bulk-order-action is-reject" title="{{ $bulkOrder->canReject() ? 'Reject' : 'Only pending requests can be rejected' }}" data-reject-action="{{ route('admin.bulk-orders.reject', $bulkOrder) }}" data-reject-name="{{ $bulkOrder->full_name }}" @disabled(! $bulkOrder->canReject())>
                                            <span class="material-symbols-outlined !text-[20px]">close</span>
                                        </button>
                                        <form action="{{ route('admin.bulk-orders.destroy', $bulkOrder) }}" method="POST" onsubmit="return confirm('Delete this bulk order request? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bulk-order-action is-delete" title="Delete">
                                                <span class="material-symbols-outlined !text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14">
                                    <div class="bulk-order-empty-state">
                                        <span class="material-symbols-outlined">inventory_2</span>
                                        <h2>No bulk order requests found</h2>
                                        <p>New product bulk inquiries from the website will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($bulkOrders->total())
                        Showing {{ $bulkOrders->firstItem() }} to {{ $bulkOrders->lastItem() }} of {{ $bulkOrders->total() }} requests
                    @else
                        No requests to show
                    @endif
                </p>
                <div>{{ $bulkOrders->links() }}</div>
            </div>
        </div>
    </div>

    <div class="bulk-order-reject-modal" id="bulk-order-reject-modal" hidden>
        <div class="bulk-order-reject-dialog">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-600">Reject Request</p>
                    <h2 class="mt-1 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Add rejection reason</h2>
                    <p class="mt-1 text-sm text-slate-500" id="bulk-order-reject-name">This reason will be sent to the customer.</p>
                </div>
                <button type="button" class="bulk-order-modal-close" aria-label="Close reject popup">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" id="bulk-order-reject-form" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-slate-700">Reason</span>
                    <textarea name="reject_reason" required rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-500/15" placeholder="Write the rejection reason"></textarea>
                </label>
                <div class="flex justify-end gap-3">
                    <button type="button" class="bulk-order-modal-cancel">Cancel</button>
                    <button type="submit" class="bulk-order-modal-submit">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .bulk-order-action {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            border: 1px solid transparent;
            transition: all 150ms ease;
        }

        .bulk-order-action.is-view { background: rgba(93, 92, 255, .08); color: #5D5CFF; }
        .bulk-order-action.is-view:hover { border-color: rgba(93, 92, 255, .25); }
        .bulk-order-action.is-approve { background: rgb(236 253 245); color: rgb(5 150 105); }
        .bulk-order-action.is-approve:hover { border-color: rgb(167 243 208); background: rgb(209 250 229); }
        .bulk-order-action.is-reject { background: rgb(254 242 242); color: rgb(220 38 38); }
        .bulk-order-action.is-reject:hover { border-color: rgb(254 202 202); background: rgb(254 226 226); color: rgb(185 28 28); }
        .bulk-order-action.is-delete { background: rgb(254 242 242); color: rgb(185 28 28); }
        .bulk-order-action.is-delete:hover { border-color: rgb(252 165 165); background: rgb(254 226 226); color: rgb(153 27 27); }
        .bulk-order-action:disabled { cursor: not-allowed; opacity: .45; }

        .bulk-order-status {
            display: inline-flex;
            border-radius: 999px;
            padding: .25rem .75rem;
            font-size: .75rem;
            font-weight: 700;
        }
        .bulk-order-status-pending { background: rgb(254 249 195); color: rgb(133 77 14); }
        .bulk-order-status-approved { background: rgb(220 252 231); color: rgb(21 128 61); }
        .bulk-order-status-rejected { background: rgb(254 226 226); color: rgb(185 28 28); }

        .bulk-order-empty-state {
            display: grid;
            justify-items: center;
            gap: .6rem;
            border-radius: 1rem;
            border: 1px dashed rgb(203 213 225);
            background: linear-gradient(180deg, rgb(248 250 252), rgb(255 255 255));
            padding: 2.5rem 1.25rem;
            text-align: center;
        }
        .bulk-order-empty-state .material-symbols-outlined {
            display: grid;
            height: 3rem;
            width: 3rem;
            place-items: center;
            border-radius: 999px;
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
            font-size: 1.45rem;
        }
        .bulk-order-empty-state h2 { font-family: 'Space Grotesk', sans-serif; font-size: 1rem; font-weight: 700; color: rgb(15 23 42); }
        .bulk-order-empty-state p { max-width: 24rem; font-size: .875rem; color: rgb(100 116 139); }

        .bulk-order-reject-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: grid;
            place-items: center;
            background: rgba(15, 23, 42, .42);
            padding: 1rem;
        }
        .bulk-order-reject-modal[hidden] { display: none; }
        .bulk-order-reject-dialog {
            width: min(100%, 31rem);
            border-radius: 1.25rem;
            border: 1px solid rgb(226 232 240);
            background: #fff;
            padding: 1.5rem;
            box-shadow: 0 24px 70px -28px rgba(15, 23, 42, .55);
        }
        .bulk-order-modal-close {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            color: rgb(100 116 139);
            transition: all 150ms ease;
        }
        .bulk-order-modal-close:hover { background: rgb(248 250 252); color: rgb(15 23 42); }
        .bulk-order-modal-cancel {
            border-radius: .75rem;
            border: 1px solid rgb(226 232 240);
            padding: .75rem 1rem;
            font-size: .875rem;
            font-weight: 700;
            color: rgb(71 85 105);
        }
        .bulk-order-modal-submit {
            border-radius: .75rem;
            background: rgb(220 38 38);
            padding: .75rem 1rem;
            font-size: .875rem;
            font-weight: 700;
            color: #fff;
        }
    </style>

    <script>
        const rejectModal = document.getElementById('bulk-order-reject-modal');
        const rejectForm = document.getElementById('bulk-order-reject-form');
        const rejectName = document.getElementById('bulk-order-reject-name');

        document.querySelectorAll('[data-reject-action]').forEach((button) => {
            button.addEventListener('click', () => {
                rejectForm.action = button.dataset.rejectAction;
                rejectName.textContent = `This reason will be sent to ${button.dataset.rejectName}.`;
                rejectModal.hidden = false;
            });
        });

        document.querySelectorAll('.bulk-order-modal-close, .bulk-order-modal-cancel').forEach((button) => {
            button.addEventListener('click', () => {
                rejectModal.hidden = true;
                rejectForm.reset();
            });
        });

        rejectModal.addEventListener('click', (event) => {
            if (event.target === rejectModal) {
                rejectModal.hidden = true;
                rejectForm.reset();
            }
        });
    </script>
@endsection
