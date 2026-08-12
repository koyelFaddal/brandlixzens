@extends('admin.layouts.app')

@section('title', 'Add Coupon')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Coupons</p>
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Add Coupon</h1>
            <p class="mt-1 text-sm text-slate-500">Create a coupon for all products or selected active products.</p>
        </div>

        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            @include('admin.coupons._form')
        </form>
    </div>
@endsection
