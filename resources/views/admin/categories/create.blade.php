@extends('admin.layouts.app')

@section('title', 'New Category')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Category</p>
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">New Category</h1>
            <p class="mt-1 text-sm text-slate-500">Create a product category with image and active status.</p>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
@endsection
