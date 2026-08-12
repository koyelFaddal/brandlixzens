@extends('admin.layouts.app')

@section('title', 'View Contact')

@section('content')
    @php
        $attachmentPath = $contact->attachment ?: $contact->file_path;
        $hasAttachment = $attachmentPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($attachmentPath);
    @endphp

    <div class="w-full max-w-5xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-[#5D5CFF]">Contact Details</p>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $contact->full_name ?: $contact->name ?: 'Contact' }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $contact->email ?: '-' }}</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('admin.contact') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold uppercase tracking-widest text-slate-600 transition hover:border-[#5D5CFF] hover:text-[#5D5CFF]">
                    Back
                </a>
                <form action="{{ route('admin.contact.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this contact submission? This will also delete the uploaded file if one exists.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="contact-delete-button inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-600 bg-red-600 px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-red-600/20 transition hover:border-red-700 hover:bg-red-700 sm:w-auto">
                        <span class="material-symbols-outlined !text-[20px]">delete</span>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <section class="contact-detail-section">
            <h2 class="mb-4 font-['Space_Grotesk'] text-lg font-bold text-slate-950">Submission Information</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="contact-detail-card">
                    <span>Name</span>
                    <p>{{ $contact->full_name ?: $contact->name ?: '-' }}</p>
                </div>
                <div class="contact-detail-card">
                    <span>Email</span>
                    <p>{{ $contact->email ?: '-' }}</p>
                </div>
                <div class="contact-detail-card">
                    <span>Phone</span>
                    <p>{{ $contact->phone ?: '-' }}</p>
                </div>
                <div class="contact-detail-card">
                    <span>Subject</span>
                    <p>{{ $contact->subject ?: '-' }}</p>
                </div>
                <div class="contact-detail-card md:col-span-2">
                    <span>Message</span>
                    <p class="whitespace-pre-line">{{ $contact->message ?: data_get($contact->form_data, 'message', '-') }}</p>
                </div>
                <div class="contact-detail-card">
                    <span>Uploaded File</span>
                    @if ($hasAttachment)
                        <p>
                            <a href="{{ route('admin.contact.file', $contact) }}" class="inline-flex items-center gap-2 text-[#5D5CFF] transition hover:text-[#4544d8]">
                                <span class="material-symbols-outlined !text-[18px]">download</span>
                                {{ $contact->file_original_name ?: basename($attachmentPath) }}
                            </a>
                        </p>
                    @else
                        <p>No file uploaded.</p>
                    @endif
                </div>
                <div class="contact-detail-card">
                    <span>Created Date</span>
                    <p>{{ $contact->created_at ? $contact->created_at->format('M d, Y h:i A') : '-' }}</p>
                </div>
            </div>
        </section>
    </div>

    <style>
        .contact-detail-section {
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background: white;
            padding: 1.5rem;
            box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.45);
        }

        @media (min-width: 640px) {
            .contact-detail-section {
                padding: 1.75rem;
            }
        }

        .contact-detail-card {
            border-radius: 1rem;
            border: 1px solid rgb(241 245 249);
            background: rgb(248 250 252);
            padding: 1rem;
        }

        .contact-detail-card span {
            display: block;
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgb(148 163 184);
        }

        .contact-detail-card p {
            margin-top: 0.5rem;
            overflow-wrap: anywhere;
            font-size: 0.875rem;
            font-weight: 600;
            color: rgb(30 41 59);
        }

        .contact-delete-button {
            background: rgb(220 38 38) !important;
            border-color: rgb(220 38 38) !important;
            color: white !important;
        }

        .contact-delete-button:hover {
            background: rgb(185 28 28) !important;
            border-color: rgb(185 28 28) !important;
        }
    </style>
@endsection
