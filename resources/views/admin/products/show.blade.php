@extends('admin.layouts.app')

@section('title', 'View Product')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Product Details</p>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $product->sku }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                    Back
                </a>
                <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                    <span class="material-symbols-outlined !text-[20px]">edit</span>
                    Edit
                </a>
            </div>
        </div>

        <div class="product-show-layout">
            <div class="product-detail-stack">
                <section class="show-section">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Product Information</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="detail-card">
                            <span>Product Name</span>
                            <p>{{ $product->name }}</p>
                        </div>
                        <div class="detail-card">
                            <span>SKU</span>
                            <p>{{ $product->sku }}</p>
                        </div>
                        <div class="detail-card md:col-span-2">
                            <span>Categories</span>
                            <p>{{ $product->categories->pluck('name')->join(', ') ?: '-' }}</p>
                        </div>
                        <div class="detail-card md:col-span-2">
                            <span>Short Description</span>
                            <p>{{ $product->short_description ?: '-' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Material</span>
                            <p>{{ $product->material ?: '-' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Color</span>
                            <p>{{ $product->color ?: '-' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Size</span>
                            <p>{{ $product->size ?: '-' }}</p>
                        </div>
                    </div>
                </section>

                <section class="show-section">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Description</h2>
                    <div class="prose-content text-sm leading-7 text-slate-600">
                        {!! $product->description ?: '<p>-</p>' !!}
                    </div>
                </section>

                <section class="show-section">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Advantages & Material Notes</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="detail-card">
                            <span>Advantages</span>
                            <div class="prose-content mt-2 text-sm leading-6 text-slate-700">
                                {!! $product->advantages ?: '<p>-</p>' !!}
                            </div>
                        </div>
                        <div class="detail-card">
                            <span>Material Notes</span>
                            <div class="prose-content mt-2 text-sm leading-6 text-slate-700">
                                {!! $product->material_notes ?: '<p>-</p>' !!}
                            </div>
                        </div>
                    </div>
                </section>

                <section class="show-section">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Product Images</h2>
                    @if ($product->images->isNotEmpty())
                        <div class="product-image-grid">
                            @foreach ($product->images as $image)
                                <div class="product-image-card">
                                    <img src="{{ asset('storage/'.$image->image) }}" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">No images uploaded.</p>
                    @endif
                </section>
            </div>

            <aside class="product-detail-stack">
                <section class="show-section">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Pricing & Stock</h2>
                    <div class="detail-card-list">
                        <div class="detail-card">
                            <span>Price</span>
                            <p>{{ number_format((float) $product->price, 2) }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Discounted Price</span>
                            <p>{{ $product->discounted_price !== null ? number_format((float) $product->discounted_price, 2) : '-' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Dealer Price</span>
                            <p>{{ number_format((float) $product->dealer_price, 2) }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Quantity</span>
                            <p>{{ $product->quantity }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Current Stock Status</span>
                            <p class="{{ $product->quantity > 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $product->stock_status }}</p>
                        </div>
                    </div>
                </section>

                <section class="show-section">
                    <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Settings</h2>
                    <div class="detail-card-list">
                        <div class="detail-card">
                            <span>Recommended Product</span>
                            <p>{{ $product->is_recommended ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Expiry Date</span>
                            <p>{{ $product->expiry_date ? $product->expiry_date->format('M d, Y') : '-' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Status</span>
                            <p class="capitalize">{{ $product->status }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Created Date</span>
                            <p>{{ $product->created_at ? $product->created_at->format('M d, Y h:i A') : '-' }}</p>
                        </div>
                        <div class="detail-card">
                            <span>Updated Date</span>
                            <p>{{ $product->updated_at ? $product->updated_at->format('M d, Y h:i A') : '-' }}</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <style>
        .product-detail-stack {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .product-show-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 2rem;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .product-show-layout {
                grid-template-columns: minmax(0, 1fr) 360px;
            }
        }

        .show-section {
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background: white;
            padding: 1.5rem;
            box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.45);
        }

        @media (min-width: 640px) {
            .show-section {
                padding: 1.75rem;
            }
        }

        .detail-card {
            border-radius: 1rem;
            border: 1px solid rgb(241 245 249);
            background: rgb(248 250 252);
            padding: 1rem;
        }

        .detail-card-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .detail-card span {
            display: block;
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgb(148 163 184);
        }

        .detail-card p {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgb(30 41 59);
        }

        .prose-content ul {
            margin-left: 1.25rem;
            list-style: disc;
        }

        .prose-content ol {
            margin-left: 1.25rem;
            list-style: decimal;
        }

        .prose-content strong {
            font-weight: 700;
            color: rgb(15 23 42);
        }

        .product-image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .product-image-card {
            overflow: hidden;
            border-radius: 0.75rem;
            border: 1px solid rgb(226 232 240);
            background: rgb(248 250 252);
        }

        .product-image-card img {
            height: 9.5rem;
            width: 100%;
            object-fit: cover;
        }
    </style>
@endsection
