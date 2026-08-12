@extends('admin.layouts.app')

@section('title', 'Sent Mail History')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Newsletter</p>
            <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Sent Mail History</h1>
            <p class="mt-2 break-words text-sm text-slate-500">{{ $subscriber->email }}</p>
        </div>
        <a href="{{ route('admin.newsletter.index') }}" class="rounded-lg bg-[#5D5CFF] font-bold text-white shadow-sm transition hover:bg-[#4b4ae6]" style="display: inline-flex !important; flex: 0 0 auto !important; align-self: flex-start !important; width: auto !important; min-width: 0 !important; max-width: max-content !important; height: 32px !important; min-height: 32px !important; max-height: 32px !important; align-items: center !important; gap: 5px !important; padding: 0 10px !important; font-size: 11px !important; line-height: 1 !important;">
            <span class="material-symbols-outlined" style="font-size: 16px !important;">arrow_back</span>
            Back to Subscribers
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Sent Date &amp; Time</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Attachment</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sentMails as $mail)
                        <tr class="transition hover:bg-slate-50">
                            <td class="break-words px-5 py-4 text-sm font-semibold text-slate-900">{{ $mail->subject }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $mail->sent_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-4">
                                @if ($mail->attachment_path)
                                    <a href="{{ route('admin.newsletter.history.attachment', [$subscriber, $mail]) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#5D5CFF] hover:underline">
                                        <span class="material-symbols-outlined !text-[18px]">download</span>
                                        Download
                                    </a>
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.newsletter.history.show', [$subscriber, $mail]) }}" class="inline-flex items-center gap-2 rounded-lg bg-[#5D5CFF]/10 px-3 py-2 text-xs font-bold text-[#5D5CFF] transition hover:bg-[#5D5CFF]/15">
                                    <span class="material-symbols-outlined !text-[17px]">visibility</span>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-14 text-center text-sm text-slate-500">No emails have been sent to this subscriber.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $sentMails->links() }}</div>
@endsection
