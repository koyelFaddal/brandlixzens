@extends('admin.layouts.app')

@section('title', 'Edit Coupon')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Coupons</p>
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Edit Coupon</h1>
            <p class="mt-1 text-sm text-slate-500">Update coupon details, validity, status, and product mapping.</p>
        </div>

        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.coupons._form')
        </form>
    </div>
@endsection
