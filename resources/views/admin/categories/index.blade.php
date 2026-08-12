@extends('admin.layouts.app')

@section('title', 'All Categories')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">All Categories</h1>
                <p class="mt-1 text-sm text-slate-500">Manage leather product categories, images, and active status.</p>
            </div>

            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                <span class="material-symbols-outlined !text-[20px]">add</span>
                New Category
            </a>
        </div>

        @error('category')
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('admin.categories.index') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search by category name">
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ $categories->total() }} categor{{ $categories->total() === 1 ? 'y' : 'ies' }} found</span>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-primary hover:text-primary">
                    Search
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Image</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <img src="{{ asset('storage/'.$category->image) }}" loading="lazy" alt="{{ $category->name }}" class="h-16 w-24 rounded-xl border border-slate-100 object-cover">
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $category->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">#{{ $category->id }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="max-w-md truncate text-sm text-slate-600" title="{{ $category->description }}">{{ $category->description ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <form action="{{ route('admin.categories.status', $category) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="status-toggle {{ $category->is_active ? 'is-active' : '' }}" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }} category" aria-label="{{ $category->is_active ? 'Deactivate' : 'Activate' }} category">
                                            <span></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="action-button" title="Edit">
                                            <span class="material-symbols-outlined !text-[20px]">edit</span>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category? This will also delete its image.');">
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
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    @if ($categories->total())
                        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} categories
                    @else
                        No categories to show
                    @endif
                </p>
                <div>
                    {{ $categories->links() }}
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

        .status-toggle {
            position: relative;
            display: inline-flex;
            height: 1.75rem;
            width: 3.25rem;
            align-items: center;
            border-radius: 9999px;
            background: rgb(203 213 225);
            transition: background 150ms ease;
        }

        .status-toggle span {
            position: absolute;
            left: 0.25rem;
            top: 0.25rem;
            height: 1.25rem;
            width: 1.25rem;
            border-radius: 9999px;
            background: white;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.2);
            transition: transform 150ms ease;
        }

        .status-toggle.is-active {
            background: #5D5CFF;
        }

        .status-toggle.is-active span {
            transform: translateX(1.5rem);
        }
    </style>
@endsection
