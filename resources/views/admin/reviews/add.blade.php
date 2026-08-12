@extends('admin.layouts.app')

@section('title', 'Reviews')

@section('content')
    <div class="w-full max-w-3xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <h1 class="font-['Space_Grotesk'] text-2xl font-bold tracking-tight text-slate-950">Customer Reviews</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Reviews are submitted by customers from delivered order items. Use the Reviews list to view or delete submitted reviews.</p>
            <a href="{{ route('admin.reviews.index') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                <span class="material-symbols-outlined !text-[20px]">table_rows</span>
                View Reviews
            </a>
        </section>
    </div>
@endsection
