@extends('admin.layouts.app')

@section('title', 'Contact')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Contact</p>
            <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Contact Submissions</h1>
            <p class="mt-2 text-sm text-slate-500">Review website contact form messages and attachments.</p>
        </div>

        <form method="GET" action="{{ route('admin.contact') }}" class="flex w-full gap-2 sm:w-auto">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search name, email, or phone"
                class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none transition focus:border-[#5D5CFF] focus:ring-4 focus:ring-[#5D5CFF]/10 sm:w-72"
            >
            <button type="submit" class="rounded-lg bg-slate-950 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                Search
            </button>
        </form>
    </div>

    @error('contact')
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ $message }}
        </div>
    @enderror

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] table-fixed divide-y divide-slate-200">
                <colgroup>
                    <col class="w-[20%]">
                    <col class="w-[24%]">
                    <col class="w-[16%]">
                    <col class="w-[26%]">
                    <col class="w-[14%]">
                </colgroup>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Phone</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($contacts as $contact)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $contact->full_name ?: $contact->name ?: '-' }}</td>
                            <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $contact->email ?: '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $contact->phone ?: '-' }}</td>
                            <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $contact->subject ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.contact.show', $contact) }}" class="contact-action-button is-view" title="View">
                                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.contact.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this contact submission? This will also delete the uploaded file if one exists.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="contact-action-button is-delete" title="Delete">
                                            <span class="material-symbols-outlined !text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-14">
                                <div class="contact-empty-state">
                                    <span class="material-symbols-outlined">inbox</span>
                                    <h2>No contact submissions found</h2>
                                    <p>New contact messages from the website will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $contacts->links() }}
    </div>

    <style>
        .contact-action-button {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            border: 1px solid transparent;
            transition: all 150ms ease;
        }

        .contact-action-button.is-view {
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
        }

        .contact-action-button.is-view:hover {
            border-color: rgba(93, 92, 255, .25);
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
        }

        .contact-action-button.is-delete {
            background: rgb(254 242 242);
            color: rgb(220 38 38);
        }

        .contact-action-button.is-delete:hover {
            border-color: rgb(254 202 202);
            background: rgb(254 226 226);
            color: rgb(185 28 28);
        }

        .contact-empty-state {
            display: grid;
            justify-items: center;
            gap: 0.6rem;
            border-radius: 1rem;
            border: 1px dashed rgb(203 213 225);
            background: linear-gradient(180deg, rgb(248 250 252), rgb(255 255 255));
            padding: 2.5rem 1.25rem;
            text-align: center;
        }

        .contact-empty-state .material-symbols-outlined {
            display: grid;
            height: 3rem;
            width: 3rem;
            place-items: center;
            border-radius: 999px;
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
            font-size: 1.45rem;
        }

        .contact-empty-state h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: rgb(15 23 42);
        }

        .contact-empty-state p {
            max-width: 24rem;
            font-size: 0.875rem;
            color: rgb(100 116 139);
        }
    </style>
@endsection
