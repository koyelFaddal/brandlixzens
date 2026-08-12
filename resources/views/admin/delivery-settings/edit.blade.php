@extends('admin.layouts.app')

@section('title', 'Delivery Settings')

@section('content')
    <div class="w-full max-w-3xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Delivery Settings</h1>
            <p class="mt-1 text-sm text-slate-500">Configure checkout delivery charges used by the customer storefront.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                Please fix the highlighted fields and try again.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.delivery-settings.update') }}" class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            @method('PUT')

            <div class="grid gap-5">
                <label class="block space-y-1">
                    <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Fixed Delivery Charge *</span>
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="fixed_delivery_charge"
                        value="{{ old('fixed_delivery_charge', $settings->fixed_delivery_charge) }}"
                        class="setting-input @error('fixed_delivery_charge') border-red-300 bg-red-50 @enderror"
                        placeholder="150"
                        required>
                    @error('fixed_delivery_charge')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block space-y-1">
                    <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Minimum Order Amount for Free Delivery *</span>
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="free_delivery_minimum"
                        value="{{ old('free_delivery_minimum', $settings->free_delivery_minimum) }}"
                        class="setting-input @error('free_delivery_minimum') border-red-300 bg-red-50 @enderror"
                        placeholder="10000"
                        required>
                    @error('free_delivery_minimum')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0">
                    <span class="material-symbols-outlined !text-[20px]">save</span>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <style>
        .setting-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid rgb(226 232 240);
            background: rgb(248 250 252);
            padding: 0.75rem;
            font-size: 0.875rem;
            outline: none;
            transition: all 150ms ease;
        }

        .setting-input:focus {
            border-color: #5D5CFF;
            background: white;
            box-shadow: 0 0 0 3px rgba(93, 92, 255, 0.15);
        }

        .field-error {
            display: block;
            min-height: 1rem;
            padding-left: 0.25rem;
            font-size: 0.75rem;
            color: rgb(220 38 38);
        }
    </style>
@endsection
