@extends('admin.layouts.app')

@section('title', 'View Bulk Order')

@section('content')
    <div class="bulk-order-show-page">
    <div class="bulk-order-show-header">
        <div class="bulk-order-show-title">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Bulk Order Details</p>
            <h1 class="mt-2 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $bulkOrder->product_name }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ $bulkOrder->full_name }} &middot; {{ $bulkOrder->email }}</p>
        </div>
        <div class="bulk-order-show-actions">
            <a href="{{ route('admin.bulk-orders.index') }}" class="bulk-order-header-action is-back">
                Back
            </a>
            <form action="{{ route('admin.bulk-orders.approve', $bulkOrder) }}" method="POST" class="bulk-order-action-form">
                @csrf
                @method('PATCH')
                <button type="submit" class="bulk-order-header-action is-approve" @disabled(! $bulkOrder->canApprove())>
                    Approve
                </button>
            </form>
            <button type="button" class="bulk-order-show-reject" @disabled(! $bulkOrder->canReject())>
                Reject
            </button>
            <form action="{{ route('admin.bulk-orders.destroy', $bulkOrder) }}" method="POST" class="bulk-order-action-form" onsubmit="return confirm('Delete this bulk order request? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bulk-order-header-action is-delete">
                    <span class="material-symbols-outlined !text-[18px]">delete</span>
                    Delete
                </button>
            </form>
        </div>
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

    <section class="bulk-order-show-section">
        <div class="bulk-order-show-grid">
            <div class="bulk-order-product-panel">
                @if ($bulkOrder->product_image)
                    <div class="bulk-order-product-image-card">
                        <img src="{{ asset('storage/'.$bulkOrder->product_image) }}" alt="{{ $bulkOrder->product_name }}" loading="lazy">
                    </div>
                @else
                    <div class="bulk-order-product-image-empty">
                        <span class="material-symbols-outlined !text-[44px]">image</span>
                    </div>
                @endif
                <span class="bulk-order-status bulk-order-status-{{ $bulkOrder->status }}">{{ $bulkOrder->status_label }}</span>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div class="bulk-order-detail-card">
                    <span>Product Name</span>
                    <strong>{{ $bulkOrder->product_name }}</strong>
                </div>
                <div class="bulk-order-detail-card">
                    <span>Customer Name</span>
                    <strong>{{ $bulkOrder->full_name }}</strong>
                </div>
                <div class="bulk-order-detail-card">
                    <span>Email</span>
                    <strong>{{ $bulkOrder->email }}</strong>
                </div>
                <div class="bulk-order-detail-card">
                    <span>Phone</span>
                    <strong>{{ $bulkOrder->phone }}</strong>
                </div>
                <div class="bulk-order-detail-card">
                    <span>Quantity</span>
                    <strong>{{ $bulkOrder->quantity }}</strong>
                </div>
                <div class="bulk-order-detail-card">
                    <span>Created Date</span>
                    <strong>{{ $bulkOrder->created_at?->format('M d, Y h:i A') }}</strong>
                </div>
                <div class="bulk-order-detail-card md:col-span-2">
                    <span>Address</span>
                    <strong>{{ $bulkOrder->address }}</strong>
                </div>
                @if ($bulkOrder->reject_reason)
                    <div class="bulk-order-detail-card md:col-span-2">
                        <span>Reject Reason</span>
                        <strong>{{ $bulkOrder->reject_reason }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="bulk-order-reject-modal" id="bulk-order-reject-modal" hidden>
        <div class="bulk-order-reject-dialog">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-600">Reject Request</p>
                    <h2 class="mt-1 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Add rejection reason</h2>
                    <p class="mt-1 text-sm text-slate-500">This reason will be sent to {{ $bulkOrder->full_name }}.</p>
                </div>
                <button type="button" class="bulk-order-modal-close" aria-label="Close reject popup">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.bulk-orders.reject', $bulkOrder) }}" class="mt-5 space-y-4" id="bulk-order-reject-form">
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

    </div>

    <style>
        .bulk-order-show-page {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            padding-bottom: 4rem;
        }

        .bulk-order-show-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .bulk-order-show-title {
            min-width: 0;
        }

        .bulk-order-show-title h1,
        .bulk-order-show-title p {
            overflow-wrap: anywhere;
        }

        .bulk-order-show-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: center;
        }

        .bulk-order-action-form {
            display: contents;
        }

        .bulk-order-header-action,
        .bulk-order-show-reject {
            min-width: 7.5rem;
            height: 3.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            border-radius: .75rem;
            padding: 0 1.25rem;
            font-size: .875rem;
            font-weight: 800;
            letter-spacing: .12em;
            line-height: 1;
            text-transform: uppercase;
            transition: background 150ms ease, border-color 150ms ease, color 150ms ease, opacity 150ms ease;
        }

        .bulk-order-header-action.is-back {
            border: 1px solid rgb(226 232 240);
            color: rgb(51 65 85);
            background: #fff;
        }

        .bulk-order-header-action.is-back:hover {
            border-color: #5D5CFF;
            color: #5D5CFF;
        }

        .bulk-order-header-action.is-approve {
            border: 0;
            color: #fff;
            background: rgb(5 150 105);
        }

        .bulk-order-header-action.is-approve:hover {
            background: rgb(4 120 87);
        }

        .bulk-order-header-action.is-delete {
            border: 0;
            color: #fff;
            background: rgb(185 28 28);
        }

        .bulk-order-header-action.is-delete:hover {
            background: rgb(153 27 27);
        }

        .bulk-order-header-action:disabled,
        .bulk-order-show-reject:disabled {
            cursor: not-allowed;
            opacity: .5;
        }

        .bulk-order-show-section {
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background: #fff;
            padding: 1.5rem;
            box-shadow: 0 18px 45px -34px rgba(15, 23, 42, .45);
        }

        .bulk-order-show-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        .bulk-order-product-panel {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .bulk-order-product-image-card,
        .bulk-order-product-image-empty {
            width: min(100%, 12.5rem);
            height: 12rem;
            overflow: hidden;
            border-radius: .75rem;
            border: 1px solid rgb(226 232 240);
            background: rgb(248 250 252);
        }

        .bulk-order-product-image-card img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .bulk-order-product-image-empty {
            display: grid;
            place-items: center;
            border-style: dashed;
            color: rgb(203 213 225);
        }

        @media (min-width: 768px) {
            .bulk-order-show-header {
                flex-direction: row;
                align-items: flex-start;
                justify-content: space-between;
            }

            .bulk-order-show-actions {
                justify-content: flex-end;
                flex-shrink: 0;
            }
        }

        @media (min-width: 1024px) {
            .bulk-order-show-grid {
                grid-template-columns: 14rem minmax(0, 1fr);
            }
        }

        @media (max-width: 480px) {
            .bulk-order-show-section {
                padding: 1rem;
            }

            .bulk-order-show-actions,
            .bulk-order-header-action,
            .bulk-order-show-reject {
                width: 100%;
            }

            .bulk-order-product-image-card,
            .bulk-order-product-image-empty {
                width: 100%;
                max-width: 12.5rem;
            }
        }

        .bulk-order-detail-card {
            border-radius: 1rem;
            border: 1px solid rgb(241 245 249);
            background: rgb(248 250 252);
            padding: 1.25rem;
        }
        .bulk-order-detail-card span {
            display: block;
            margin-bottom: .7rem;
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgb(148 163 184);
        }
        .bulk-order-detail-card strong {
            display: block;
            overflow-wrap: anywhere;
            font-size: 1rem;
            color: rgb(15 23 42);
        }
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
        .bulk-order-show-reject {
            border: 0;
            background: rgb(220 38 38);
            color: #fff;
        }
        .bulk-order-show-reject:hover { background: rgb(185 28 28); }
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
        const rejectOpen = document.querySelector('.bulk-order-show-reject');

        rejectOpen?.addEventListener('click', () => {
            rejectModal.hidden = false;
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
