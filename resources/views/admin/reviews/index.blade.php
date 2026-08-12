@extends('admin.layouts.app')

@section('title', 'Reviews')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Reviews</h1>
            <p class="mt-1 text-sm text-slate-500">Review customer ratings, uploaded photos, comments, and purchase context.</p>
        </div>

        <form action="{{ route('admin.reviews.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search customer, product, SKU, or order">
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                Search
            </button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1080px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Customer</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Product</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Order</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Rating</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Images</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Review Date</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reviews as $review)
                            @php
                                $ratings = collect([$review->overall_rating, $review->quality_rating, $review->material_rating, $review->delivery_rating])->filter();
                                $averageRating = round($ratings->avg() ?: 0, 1);
                            @endphp
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $review->user?->name ?: 'Verified Customer' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $review->user?->email ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $review->product?->name ?: $review->orderItem?->product_name ?: '-' }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $review->product?->sku ?: $review->orderItem?->product_sku ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $review->order?->order_number ?: '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                        <span class="material-symbols-outlined !text-[16px]" style="font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 20;">star</span>
                                        {{ number_format($averageRating, 1) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $review->images->count() }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $review->created_at?->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.reviews.show', $review) }}" class="action-button" title="View">
                                            <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                        </a>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review permanently?');">
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
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">No reviews found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($reviews->total())
                        Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
                    @else
                        No reviews to show
                    @endif
                </p>
                <div>{{ $reviews->links() }}</div>
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
