@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Welcome back. Your admin session is active.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Admin User</p>
            <p class="mt-2 text-xl font-semibold text-slate-950">{{ auth()->user()->username ?: auth()->user()->email }}</p>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Session Status</p>
            <p class="mt-2 text-xl font-semibold text-emerald-700">Authenticated</p>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Last Updated</p>
            <p class="mt-2 text-xl font-semibold text-slate-950">{{ now()->format('M d, Y') }}</p>
        </section>
    </div>
@endsection
