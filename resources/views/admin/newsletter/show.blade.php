@extends('admin.layouts.app')

@section('title', 'Sent Mail Details')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Newsletter</p>
                <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Sent Mail Details</h1>
            </div>
            <a href="{{ route('admin.newsletter.history', $subscriber) }}" class="rounded-lg bg-[#5D5CFF] font-bold text-white shadow-sm transition hover:bg-[#4b4ae6]" style="display: inline-flex !important; flex: 0 0 auto !important; align-self: flex-start !important; width: auto !important; min-width: 0 !important; max-width: max-content !important; height: 32px !important; min-height: 32px !important; max-height: 32px !important; align-items: center !important; gap: 5px !important; padding: 0 10px !important; font-size: 11px !important; line-height: 1 !important;">
                <span class="material-symbols-outlined" style="font-size: 16px !important;">arrow_back</span>
                Back to History
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <dl class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-y-0">
                <div class="p-5 sm:border-r sm:border-slate-100">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">To</dt>
                    <dd class="mt-2 break-words text-sm font-semibold text-slate-800">{{ $sentMail->recipient_email }}</dd>
                </div>
                <div class="border-t border-slate-100 p-5 sm:border-t-0">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Sent Date</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-800">{{ $sentMail->sent_at->format('d M Y, h:i A') }}</dd>
                </div>
            </dl>

            <div class="border-t border-slate-100 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Subject</p>
                <p class="mt-2 break-words text-base font-bold text-slate-900">{{ $sentMail->subject }}</p>
            </div>

            <div class="border-t border-slate-100 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Body</p>
                <div class="prose mt-4 max-w-none break-words text-sm leading-7 text-slate-700">{!! $sentMail->body !!}</div>
            </div>

            <div class="border-t border-slate-100 p-5">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Attachment</p>
                @if ($sentMail->attachment_path)
                    <a href="{{ route('admin.newsletter.history.attachment', [$subscriber, $sentMail]) }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-[#5D5CFF] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#4b4ae6]">
                        <span class="material-symbols-outlined !text-[18px]">download</span>
                        Download Attachment
                    </a>
                @else
                    <p class="mt-2 text-sm text-slate-400">No attachment</p>
                @endif
            </div>
        </div>
    </div>
@endsection
