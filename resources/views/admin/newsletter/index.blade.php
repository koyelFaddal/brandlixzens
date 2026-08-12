@extends('admin.layouts.app')

@section('title', 'Newsletter')

@section('content')
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#5D5CFF]">Newsletter</p>
        <h1 class="mt-1 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Newsletter Subscribers</h1>
        <p class="mt-2 text-sm text-slate-500">View subscribers and send an individual newsletter email.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] table-fixed divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="w-[12%] px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">ID</th>
                        <th class="w-[34%] px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="w-[22%] px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Created At</th>
                        <th class="w-[32%] px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($subscribers as $subscriber)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">#{{ $subscriber->id }}</td>
                            <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $subscriber->email }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $subscriber->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.newsletter.history', $subscriber) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-[#5D5CFF]/30 hover:text-[#5D5CFF]">
                                    <span class="material-symbols-outlined !text-[17px]">history</span>
                                    View Sent Mails
                                </a>
                                <button type="button"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#5D5CFF] px-3 py-2 text-xs font-bold text-white transition hover:bg-[#4b4ae6]"
                                    data-id="{{ $subscriber->id }}"
                                    data-email="{{ $subscriber->email }}"
                                    onclick="openNewsletterModal(this)">
                                    <span class="material-symbols-outlined !text-[17px]">send</span>
                                    Send Mail
                                </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-14 text-center text-sm text-slate-500">No newsletter subscribers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $subscribers->links() }}</div>

    <div class="newsletter-modal" id="newsletter-modal" role="dialog" aria-modal="true" aria-labelledby="newsletter-modal-title">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeNewsletterModal()"></div>
        <div class="newsletter-modal-panel">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-7">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#5D5CFF]">Newsletter</p>
                    <h2 id="newsletter-modal-title" class="mt-1 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Send Mail</h2>
                </div>
                <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeNewsletterModal()" aria-label="Close">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="newsletter-mail-form" method="POST" enctype="multipart/form-data" class="min-h-0 overflow-y-auto px-5 py-5 sm:px-7">
                @csrf
                <input id="newsletter-subscriber-id" type="hidden" name="subscriber_id" value="{{ old('subscriber_id') }}">

                <div class="space-y-5">
                    <label class="block space-y-1.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">To</span>
                        <input id="newsletter-to" type="email" readonly class="newsletter-input cursor-not-allowed bg-slate-100 text-slate-500">
                    </label>

                    <label class="block space-y-1.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Subject</span>
                        <input type="text" name="subject" value="{{ old('subject') }}" maxlength="255" class="newsletter-input" required>
                    </label>

                    <div class="space-y-1.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Body</span>
                        <div class="newsletter-editor-toolbar">
                            <button type="button" data-command="bold" aria-label="Bold"><span class="material-symbols-outlined">format_bold</span></button>
                            <button type="button" data-command="italic" aria-label="Italic"><span class="material-symbols-outlined">format_italic</span></button>
                            <button type="button" data-command="underline" aria-label="Underline"><span class="material-symbols-outlined">format_underlined</span></button>
                            <button type="button" data-command="insertUnorderedList" aria-label="Bulleted list"><span class="material-symbols-outlined">format_list_bulleted</span></button>
                            <button type="button" data-command="insertOrderedList" aria-label="Numbered list"><span class="material-symbols-outlined">format_list_numbered</span></button>
                        </div>
                        <div id="newsletter-body-editor" class="newsletter-editor" contenteditable="true">{!! old('body') !!}</div>
                        <textarea id="newsletter-body" name="body" class="hidden">{{ old('body') }}</textarea>
                    </div>

                    <label class="block space-y-1.5">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            Attachment <span class="font-normal normal-case text-slate-400">(optional, max 10 MB)</span>
                        </span>
                        <input type="file" name="attachment" class="newsletter-input file:mr-4 file:rounded-lg file:border-0 file:bg-[#5D5CFF]/10 file:px-3 file:py-2 file:text-xs file:font-bold file:text-[#5D5CFF]">
                    </label>
                </div>

                <div class="mt-7 flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <button type="button" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" onclick="closeNewsletterModal()">Cancel</button>
                    <button type="submit" class="rounded-lg bg-[#5D5CFF] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#4b4ae6]">Send</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .newsletter-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .newsletter-modal.is-open {
            display: flex;
        }

        .newsletter-modal-panel {
            position: relative;
            z-index: 10;
            display: flex;
            width: min(680px, calc(100vw - 2rem));
            max-height: calc(100vh - 2rem);
            flex-direction: column;
            overflow: hidden;
            border-radius: 1rem;
            background: white;
            box-shadow: 0 25px 80px -35px rgba(15, 23, 42, .7);
        }

        .newsletter-input {
            width: 100%;
            border: 1px solid rgb(226 232 240);
            border-radius: .75rem;
            padding: .7rem .85rem;
            font-size: .875rem;
            outline: none;
        }

        .newsletter-input:focus,
        .newsletter-editor:focus {
            border-color: #5D5CFF;
            box-shadow: 0 0 0 4px rgba(93, 92, 255, .1);
        }

        .newsletter-editor-toolbar {
            display: flex;
            gap: .25rem;
            border: 1px solid rgb(226 232 240);
            border-bottom: 0;
            border-radius: .75rem .75rem 0 0;
            background: rgb(248 250 252);
            padding: .4rem;
        }

        .newsletter-editor-toolbar button {
            display: grid;
            height: 2rem;
            width: 2rem;
            place-items: center;
            border-radius: .4rem;
            color: rgb(71 85 105);
        }

        .newsletter-editor-toolbar button:hover {
            background: white;
            color: #5D5CFF;
        }

        .newsletter-editor-toolbar .material-symbols-outlined {
            font-size: 19px;
        }

        .newsletter-editor {
            min-height: 180px;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid rgb(226 232 240);
            border-radius: 0 0 .75rem .75rem;
            padding: .85rem;
            font-size: .875rem;
            line-height: 1.6;
            outline: none;
        }
    </style>

    <script>
        (() => {
            const modal = document.getElementById('newsletter-modal');
            const form = document.getElementById('newsletter-mail-form');
            const subscriberId = document.getElementById('newsletter-subscriber-id');
            const toInput = document.getElementById('newsletter-to');
            const editor = document.getElementById('newsletter-body-editor');
            const bodyInput = document.getElementById('newsletter-body');
            const routeTemplate = @json(route('admin.newsletter.send', ['newsletterSubscription' => '__ID__']));

            window.openNewsletterModal = (button) => {
                subscriberId.value = button.dataset.id;
                form.action = routeTemplate.replace('__ID__', button.dataset.id);
                toInput.value = button.dataset.email;
                modal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            };

            window.closeNewsletterModal = () => {
                modal.classList.remove('is-open');
                document.body.style.overflow = '';
            };

            document.querySelectorAll('.newsletter-editor-toolbar [data-command]').forEach((button) => {
                button.addEventListener('click', () => {
                    editor.focus();
                    document.execCommand(button.dataset.command, false);
                });
            });

            form.addEventListener('submit', () => {
                bodyInput.value = editor.innerHTML.trim();
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeNewsletterModal();
                }
            });

            @if ($errors->any() && old('subscriber_id'))
                const previousButton = document.querySelector('[data-id="{{ old('subscriber_id') }}"]');
                if (previousButton) openNewsletterModal(previousButton);
            @endif
        })();
    </script>
@endsection
