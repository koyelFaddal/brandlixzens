@extends('admin.layouts.app')

@section('title', 'Change Password')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Change Password</h1>
        <p class="mt-2 text-sm text-slate-600">Update your admin password. You will be logged out after saving.</p>
    </div>

    <section class="max-w-xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.change-password.update') }}" class="space-y-5">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700">Current Password</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                       class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                @error('current_password')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                @error('password')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.dashboard') }}" class="rounded-md border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </form>
    </section>
@endsection
