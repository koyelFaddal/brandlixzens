@extends('admin.layouts.app')

@section('title', 'All Products')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">All Products</h1>
                <p class="mt-1 text-sm text-slate-500">Manage products, stock, categories, pricing, and publication status.</p>
            </div>

            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                <span class="material-symbols-outlined !text-[20px]">add</span>
                Add New
            </a>
        </div>

        @error('product')
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('admin.products.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search by product name or SKU">
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ $products->total() }} product{{ $products->total() === 1 ? '' : 's' }} found</span>
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
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Product Thumbnail</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Product Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Price</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Dealer Price</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Stock Status</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Expiry Date</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    @if ($product->primaryImage)
                                        <img src="{{ asset('storage/'.$product->primaryImage->image) }}" loading="lazy" alt="{{ $product->name }}" class="h-16 w-24 rounded-xl border border-slate-100 object-cover">
                                    @else
                                        <div class="flex h-16 w-24 items-center justify-center rounded-xl border border-dashed border-slate-200 text-slate-300">
                                            <span class="material-symbols-outlined">image</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $product->sku }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="max-w-xs text-sm text-slate-600">{{ $product->categories->pluck('name')->join(', ') ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ number_format((float) $product->price, 2) }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800">{{ number_format((float) $product->dealer_price, 2) }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $product->quantity > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $product->stock_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $product->expiry_date ? $product->expiry_date->format('M d, Y') : '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold capitalize text-slate-700">{{ $product->status }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.products.show', $product) }}" class="action-button" title="View">
                                            <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="action-button" title="Edit">
                                            <span class="material-symbols-outlined !text-[20px]">edit</span>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product? This will also delete its images.');">
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
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($products->total())
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                    @else
                        No products to show
                    @endif
                </p>
                <div>
                    {{ $products->links() }}
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
