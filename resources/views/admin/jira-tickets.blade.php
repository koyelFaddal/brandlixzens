@extends('admin.layouts.app')

@section('title', 'Jira Tickets')

@section('content')
    @php
        $statusFilterOptions = [
            ['value' => 'To Do', 'label' => 'To Do'],
            ['value' => 'Pending', 'label' => 'Pending (In review)'],
            ['value' => 'In Progress', 'label' => 'In Progress'],
            ['value' => 'Reopened', 'label' => 'Reopen'],
            ['value' => 'Done', 'label' => 'Done'],
        ];
    @endphp

    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Jira Tickets</h1>
            <p class="mt-1 text-sm text-slate-500">Manage website support requests, Jira references, and customer-facing status updates.</p>
        </div>

        <form method="GET" action="{{ route('admin.jira-tickets.index') }}" class="mb-5 grid gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_auto] md:items-center">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-[#5D5CFF] focus:bg-white focus:ring-2 focus:ring-[#5D5CFF]/20"
                       placeholder="Search ticket, name, email, or subject">
            </div>
            <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-[#5D5CFF] focus:bg-white focus:ring-2 focus:ring-[#5D5CFF]/20">
                <option value="">All statuses</option>
                @foreach ($statusFilterOptions as $statusOption)
                    <option value="{{ $statusOption['value'] }}" @selected($selectedStatus === $statusOption['value'])>{{ $statusOption['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#5D5CFF] px-5 py-3 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-[#4b4af0]">
                <span class="material-symbols-outlined !text-[18px]">filter_alt</span>
                Filter
            </button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Ticket Number</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Jira Key</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Subject</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Priority</th>
                            <!-- <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Attachments</th> -->
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Current Status</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Created Date</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tickets as $ticket)
                            <tr class="transition hover:bg-slate-50/80" id="ticket-row-{{ $ticket->id }}">
                                <td class="px-5 py-4 font-semibold text-slate-900">#{{ $ticket->id }}</td>
                                <td class="px-5 py-4 text-sm font-semibold text-[#5D5CFF]">{{ $ticket->jira_ticket_key ?: '-' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-700 whitespace-nowrap">{{ $ticket->name }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $ticket->email }}</td>
                                <td class="max-w-[280px] truncate px-5 py-4 text-sm text-slate-700">{{ $ticket->subject }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $ticket->priority }}</td>
                                <td class="px-5 py-4">
                                    <select class="jira-status-select rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 outline-none transition focus:border-[#5D5CFF] focus:ring-2 focus:ring-[#5D5CFF]/20"
                                            data-ticket-id="{{ $ticket->id }}"
                                            data-current-status="{{ $ticket->status }}">
                                        <option value="{{ $ticket->status }}">{{ $ticket->status }}</option>
                                    </select>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500">{{ optional($ticket->created_at)->format('M d, Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <button type="button"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-[#5D5CFF]/10 hover:text-[#5D5CFF]"
                                            title="View details"
                                            onclick="viewJiraTicket({{ $ticket->id }})">
                                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-10 text-center text-sm text-slate-500">No Jira tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>

    <div class="jira-modal-overlay" id="jira-ticket-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeJiraTicketModal()"></div>
        <div class="jira-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-[#5D5CFF]">Support Ticket</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl">Ticket Details</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeJiraTicketModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="jira-modal-scroll flex-1 space-y-5 px-5 py-5 sm:px-8" id="jira-ticket-content"></div>
        </div>
    </div>

    <style>
        .jira-modal-overlay{position:fixed;inset:0;z-index:100;display:none;align-items:center;justify-content:center;padding:1rem}
        .jira-modal-overlay.is-open{display:flex}
        .jira-modal-panel{position:relative;z-index:10;display:flex;width:min(920px,calc(100vw - 2rem));max-height:calc(100vh - 2rem);flex-direction:column;overflow:hidden;border-radius:1rem;background:white;box-shadow:0 25px 80px -35px rgba(15,23,42,.7)}
        .jira-modal-scroll{min-height:0;overflow-y:auto}
    </style>

    @php
        $routes = [
            'show' => route('admin.jira-tickets.show', ['jiraTicket' => '__ID__']),
            'status' => route('admin.jira-tickets.status', ['jiraTicket' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($routes);
            const statuses = @json($statuses);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const routeFor = (name, id) => routes[name].replace('__ID__', id);
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
            const fileSize = (bytes) => `${((Number(bytes || 0)) / 1024).toFixed(1)} KB`;
            const statusKey = (value) => String(value ?? '').trim().toLowerCase();
            const displayStatus = (value) => {
                const status = String(value ?? '').trim();
                const canonicalStatuses = {
                    'open': 'To Do',
                    'to do': 'To Do',
                    'pending': 'Pending',
                    'in progress': 'In Progress',
                    'done': 'Done',
                    'reopen': 'Reopened',
                    'reopened': 'Reopened',
                };

                return canonicalStatuses[statusKey(status)] || status;
            };
            const statusLabel = (value) => {
                const status = displayStatus(value);
                const labels = {
                    'pending': 'Pending (In review)',
                    'reopened': 'Reopen',
                };

                return labels[statusKey(status)] || status;
            };
            const statusOrder = (value) => {
                const order = {
                    'to do': 0,
                    'pending': 1,
                    'in progress': 2,
                    'reopened': 3,
                    'done': 4,
                };

                return order[statusKey(displayStatus(value))] ?? 99;
            };

            const requestJson = async (url, options = {}) => {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        ...(options.headers || {}),
                    },
                    ...options,
                });
                const json = await response.json();
                if (!response.ok || json.status === 'error') throw json;
                return json;
            };

            const populateStatusSelect = (select, currentStatus, transitions = []) => {
                const canonicalCurrentStatus = displayStatus(currentStatus);
                const currentStatusKey = statusKey(canonicalCurrentStatus);
                const uniqueTransitions = [];
                const seenStatuses = new Set([currentStatusKey]);

                transitions.forEach((transition) => {
                    const targetStatus = displayStatus(transition.to_status);
                    const targetStatusKey = statusKey(targetStatus);

                    if (!targetStatus || seenStatuses.has(targetStatusKey)) return;

                    seenStatuses.add(targetStatusKey);
                    uniqueTransitions.push({
                        ...transition,
                        to_status: targetStatus,
                    });
                });

                uniqueTransitions.sort((first, second) => statusOrder(first.to_status) - statusOrder(second.to_status));

                select.innerHTML = '';
                select.append(new Option(statusLabel(canonicalCurrentStatus), canonicalCurrentStatus, true, true));
                select.dataset.currentStatus = canonicalCurrentStatus;

                uniqueTransitions.forEach((transition) => {
                    const label = statusLabel(transition.to_status);
                    const transitionName = String(transition.name ?? '').trim();
                    const optionText = transitionName && !label.toLowerCase().includes(transitionName.toLowerCase())
                        ? `${label} (${transitionName})`
                        : label;
                    const option = new Option(optionText, transition.to_status);
                    option.dataset.transitionId = transition.id;
                    select.append(option);
                });

                if (uniqueTransitions.length === 0) {
                    const option = new Option('No available Jira transitions', '');
                    option.disabled = true;
                    select.append(option);
                }
            };

            const loadTransitionsForSelect = async (select, silent = false) => {
                const ticketId = select.dataset.ticketId;
                select.disabled = true;
                try {
                    const { data: ticket } = await requestJson(routeFor('show', ticketId));
                    populateStatusSelect(select, ticket.status, ticket.available_transitions || []);
                    select.dataset.transitionsLoaded = 'true';
                } catch (error) {
                    if (!silent) {
                        alert(error.message || 'Unable to load Jira transitions.');
                    }
                } finally {
                    select.disabled = false;
                }
            };

            document.querySelectorAll('.jira-status-select').forEach((select) => {
                populateStatusSelect(select, select.dataset.currentStatus, []);

                select.addEventListener('focus', () => {
                    if (select.dataset.transitionsLoaded !== 'true') {
                        loadTransitionsForSelect(select);
                    }
                });
                select.addEventListener('mousedown', () => {
                    if (select.dataset.transitionsLoaded !== 'true') {
                        loadTransitionsForSelect(select, true);
                    }
                });

                select.addEventListener('change', async () => {
                    if (!select.value || statusKey(select.value) === statusKey(select.dataset.currentStatus)) return;

                    const ticketId = select.dataset.ticketId;
                    const previousStatus = select.dataset.currentStatus;
                    select.disabled = true;
                    try {
                        await requestJson(routeFor('status', ticketId), {
                            method: 'PATCH',
                            body: JSON.stringify({ status: displayStatus(select.value) }),
                        }).then(({ data }) => {
                            select.dataset.currentStatus = data.status;
                            populateStatusSelect(select, data.status, data.available_transitions || []);
                            select.dataset.transitionsLoaded = 'true';
                        });
                    } catch (error) {
                        alert(error.message || 'Unable to update ticket status.');
                        select.value = previousStatus;
                    } finally {
                        select.disabled = false;
                    }
                });

                window.setTimeout(() => loadTransitionsForSelect(select, true), 0);
            });

            window.viewJiraTicket = async (id) => {
                const modal = document.getElementById('jira-ticket-modal');
                const content = document.getElementById('jira-ticket-content');
                content.innerHTML = '<p class="py-10 text-center text-sm text-slate-500">Loading ticket details...</p>';
                modal.classList.add('is-open');
                document.body.style.overflow = 'hidden';

                try {
                    const { data: ticket } = await requestJson(routeFor('show', id));
                    content.innerHTML = `
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-[#5D5CFF]">${escapeHtml(ticket.jira_ticket_key || `#${ticket.id}`)}</p>
                            <h3 class="mt-1 font-['Space_Grotesk'] text-2xl font-bold text-slate-950">${escapeHtml(ticket.subject)}</h3>
                            <p class="mt-2 text-sm font-semibold text-slate-600">${escapeHtml(ticket.status)} &middot; ${escapeHtml(ticket.created_at)}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            ${detailCard('Name', ticket.name)}
                            ${detailCard('Email', ticket.email)}
                            ${detailCard('Priority', ticket.priority)}
                            ${detailCard('Issue Category', ticket.issue_category)}
                            ${detailCard('Jira Issue ID', ticket.jira_issue_id || '-')}
                            ${detailCard('Local Ticket Number', `#${ticket.id}`)}
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Available Jira Transitions</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                ${(ticket.available_transitions || []).length ? ticket.available_transitions.map((transition) => `<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">${escapeHtml(transition.to_status)} <span class="text-slate-400">via</span> ${escapeHtml(transition.name)}</span>`).join('') : '<span class="text-sm text-slate-500">No transitions available.</span>'}
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Issue Details</p>
                            <p class="mt-3 whitespace-pre-line break-words text-sm leading-6 text-slate-600">${escapeHtml(ticket.message)}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Attachments</p>
                            <div class="mt-3 space-y-2">
                                ${(ticket.attachments || []).length ? ticket.attachments.map((attachment) => `
                                    <a href="${escapeHtml(attachment.download_url)}" class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-700 transition hover:border-[#5D5CFF]/30 hover:bg-white">
                                        <span class="min-w-0">
                                            <span class="block truncate font-semibold">${escapeHtml(attachment.name)}</span>
                                            <span class="block text-xs text-slate-500">${escapeHtml(attachment.type || 'Unknown')} &middot; ${fileSize(attachment.size)}</span>
                                        </span>
                                        <span class="material-symbols-outlined !text-[20px] text-[#5D5CFF]">download</span>
                                    </a>
                                `).join('') : '<span class="text-sm text-slate-500">No attachments submitted.</span>'}
                            </div>
                        </div>
                    `;
                } catch (error) {
                    content.innerHTML = `<p class="py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load ticket.')}</p>`;
                }
            };

            window.closeJiraTicketModal = () => {
                document.getElementById('jira-ticket-modal').classList.remove('is-open');
                document.body.style.overflow = '';
            };

            const detailCard = (label, value) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">${escapeHtml(value || '-')}</p>
                </div>
            `;
        })();
    </script>
@endsection
