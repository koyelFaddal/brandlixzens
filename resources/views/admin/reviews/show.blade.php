@extends('admin.layouts.app')

@section('title', 'Review Details')

@section('content')
    <div class="w-full max-w-6xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <a href="{{ route('admin.reviews.index') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-primary">
                    <span class="material-symbols-outlined !text-[18px]">arrow_back</span>
                    Back to Reviews
                </a>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Review Details</h1>
                <p class="mt-1 text-sm text-slate-500">Submitted on {{ $review->created_at?->format('M d, Y h:i A') }}</p>
            </div>

            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-100 bg-red-50 px-5 py-3 text-sm font-bold uppercase tracking-widest text-red-600 transition hover:bg-red-600 hover:text-white">
                    <span class="material-symbols-outlined !text-[20px]">delete</span>
                    Delete Review
                </button>
            </form>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_360px]">
            <div class="space-y-5">
                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950">Rating Summary</h2>
                        <span class="inline-flex w-max items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-sm font-bold text-amber-700">
                            <span class="material-symbols-outlined !text-[18px]" style="font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 20;">star</span>
                            {{ number_format($averageRating, 1) }} Average
                        </span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Overall Rating</span>
                            <strong class="mt-1 block text-lg text-slate-950">{{ $review->overall_rating }} / 5</strong>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Product Quality</span>
                            <strong class="mt-1 block text-lg text-slate-950">{{ $review->quality_rating }} / 5</strong>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Material / Build Quality</span>
                            <strong class="mt-1 block text-lg text-slate-950">{{ $review->material_rating }} / 5</strong>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Delivery Experience</span>
                            <strong class="mt-1 block text-lg text-slate-950">{{ $review->delivery_rating }} / 5</strong>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Review Content</h2>
                    <div class="space-y-4 text-sm leading-7 text-slate-600">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-widest text-slate-400">Advantages</span>
                            <p class="mt-1 text-slate-800">{{ $review->advantages ?: '-' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-widest text-slate-400">Disadvantages</span>
                            <p class="mt-1 text-slate-800">{{ $review->disadvantages ?: '-' }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-widest text-slate-400">Comments</span>
                            <p class="mt-1 text-slate-800">{{ $review->comments ?: '-' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Uploaded Images</h2>
                    @if ($review->images->isNotEmpty())
                        <div class="admin-review-image-grid">
                            @foreach ($review->images as $index => $image)
                                <button type="button" class="admin-review-image-card" data-review-image="{{ asset('storage/'.$image->image) }}" data-review-image-index="{{ $index }}" aria-label="Open review image {{ $index + 1 }}">
                                    <img src="{{ asset('storage/'.$image->image) }}" alt="Review image {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">No images uploaded.</p>
                    @endif
                </section>
            </div>

            <aside class="space-y-5">
                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Customer</h2>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">{{ $review->user?->name ?: 'Verified Customer' }}</p>
                        <p>{{ $review->user?->email ?: '-' }}</p>
                        <p>{{ $review->user?->phone ?: '-' }}</p>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Product</h2>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">{{ $review->product?->name ?: $review->orderItem?->product_name ?: '-' }}</p>
                        <p>SKU: {{ $review->product?->sku ?: $review->orderItem?->product_sku ?: '-' }}</p>
                        <p>Price: {{ $review->product ? '₹'.number_format((float) $review->product->price, 2) : '-' }}</p>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Order</h2>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">{{ $review->order?->order_number ?: '-' }}</p>
                        <p>{{ $review->order?->ordered_at?->format('M d, Y h:i A') ?: '-' }}</p>
                        @if ($review->order)
                            <a href="{{ route('admin.orders.show', $review->order) }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary">
                                View Order
                                <span class="material-symbols-outlined !text-[16px]">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Order Item</h2>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">{{ $review->orderItem?->product_name ?: '-' }}</p>
                        <p>Unit Price: {{ $review->orderItem ? '₹'.number_format((float) $review->orderItem->unit_price, 2) : '-' }}</p>
                        <p>Quantity: {{ $review->orderItem?->quantity ?: '-' }}</p>
                        <p>Line Total: {{ $review->orderItem ? '₹'.number_format((float) $review->orderItem->line_total, 2) : '-' }}</p>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    @if ($review->images->isNotEmpty())
        <div class="admin-review-lightbox" id="admin-review-lightbox" aria-hidden="true">
            <button type="button" class="admin-review-lightbox-backdrop" data-review-lightbox-close aria-label="Close image preview"></button>
            <div class="admin-review-lightbox-panel" role="dialog" aria-modal="true" aria-label="Review image preview">
                <button type="button" class="admin-review-lightbox-close" data-review-lightbox-close aria-label="Close image preview">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <button type="button" class="admin-review-lightbox-nav is-prev" data-review-lightbox-prev aria-label="Previous image">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <img id="admin-review-lightbox-image" src="" alt="Review image preview">
                <button type="button" class="admin-review-lightbox-nav is-next" data-review-lightbox-next aria-label="Next image">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
                <span class="admin-review-lightbox-count" id="admin-review-lightbox-count"></span>
            </div>
        </div>
    @endif

    <style>
        .admin-review-image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            max-width: 42rem;
        }

        .admin-review-image-card {
            overflow: hidden;
            border: 1px solid rgb(226 232 240);
            border-radius: 0.75rem;
            background: rgb(248 250 252);
            cursor: zoom-in;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .admin-review-image-card:hover {
            border-color: rgba(91, 79, 255, 0.35);
            box-shadow: 0 18px 36px -28px rgba(15, 23, 42, 0.65);
            transform: translateY(-1px);
        }

        .admin-review-image-card img {
            width: 100%;
            height: 9.5rem;
            display: block;
            object-fit: cover;
        }

        .admin-review-lightbox {
            position: fixed;
            inset: 0;
            z-index: 120;
            display: none;
            place-items: center;
            padding: 2rem;
        }

        .admin-review-lightbox.is-open {
            display: grid;
        }

        .admin-review-lightbox-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(15, 23, 42, 0.68);
            backdrop-filter: blur(3px);
        }

        .admin-review-lightbox-panel {
            position: relative;
            z-index: 1;
            width: min(980px, calc(100vw - 4rem));
            height: min(76vh, 720px);
            display: grid;
            place-items: center;
        }

        .admin-review-lightbox-panel img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 1rem;
            object-fit: contain;
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.42);
        }

        .admin-review-lightbox-close,
        .admin-review-lightbox-nav {
            position: absolute;
            z-index: 2;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 999px;
            color: rgb(15 23 42);
            background: white;
            box-shadow: 0 18px 36px -24px rgba(15, 23, 42, 0.75);
        }

        .admin-review-lightbox-close {
            top: -1rem;
            right: -1rem;
            width: 2.75rem;
            height: 2.75rem;
        }

        .admin-review-lightbox-nav {
            top: 50%;
            width: 3rem;
            height: 3rem;
            transform: translateY(-50%);
        }

        .admin-review-lightbox-nav.is-prev {
            left: -1.5rem;
        }

        .admin-review-lightbox-nav.is-next {
            right: -1.5rem;
        }

        .admin-review-lightbox-count {
            position: fixed;
            left: 50%;
            bottom: 1.5rem;
            z-index: 2;
            transform: translateX(-50%);
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.78);
            color: white;
            padding: 0.5rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 800;
        }
    </style>

    @if ($review->images->isNotEmpty())
        <script>
            (() => {
                const lightbox = document.getElementById('admin-review-lightbox');
                const preview = document.getElementById('admin-review-lightbox-image');
                const count = document.getElementById('admin-review-lightbox-count');
                const thumbnails = Array.from(document.querySelectorAll('[data-review-image]'));
                const closeButtons = document.querySelectorAll('[data-review-lightbox-close]');
                const previousButton = document.querySelector('[data-review-lightbox-prev]');
                const nextButton = document.querySelector('[data-review-lightbox-next]');
                const images = thumbnails.map((thumbnail) => thumbnail.dataset.reviewImage);
                let currentIndex = 0;
                let previousBodyOverflow = '';

                const renderImage = () => {
                    if (!preview || !count || !images.length) {
                        return;
                    }

                    preview.src = images[currentIndex];
                    count.textContent = `${currentIndex + 1} / ${images.length}`;
                    const showNavigation = images.length > 1;
                    previousButton.hidden = !showNavigation;
                    nextButton.hidden = !showNavigation;
                };

                const openLightbox = (index) => {
                    if (!lightbox || !images.length) {
                        return;
                    }

                    currentIndex = index;
                    previousBodyOverflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                    renderImage();
                    lightbox.classList.add('is-open');
                    lightbox.setAttribute('aria-hidden', 'false');
                };

                const closeLightbox = () => {
                    if (!lightbox) {
                        return;
                    }

                    lightbox.classList.remove('is-open');
                    lightbox.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = previousBodyOverflow;
                };

                const moveImage = (direction) => {
                    if (!images.length) {
                        return;
                    }

                    currentIndex = (currentIndex + direction + images.length) % images.length;
                    renderImage();
                };

                thumbnails.forEach((thumbnail, index) => {
                    thumbnail.addEventListener('click', () => openLightbox(index));
                });

                closeButtons.forEach((button) => {
                    button.addEventListener('click', closeLightbox);
                });

                previousButton.addEventListener('click', () => moveImage(-1));
                nextButton.addEventListener('click', () => moveImage(1));

                document.addEventListener('keydown', (event) => {
                    if (!lightbox || !lightbox.classList.contains('is-open')) {
                        return;
                    }

                    if (event.key === 'Escape') {
                        closeLightbox();
                    }

                    if (event.key === 'ArrowLeft') {
                        moveImage(-1);
                    }

                    if (event.key === 'ArrowRight') {
                        moveImage(1);
                    }
                });
            })();
        </script>
    @endif
@endsection
