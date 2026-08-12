@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Category</p>
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Edit Category</h1>
            <p class="mt-1 text-sm text-slate-500">Update category information, image, and active status.</p>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
@endsection
