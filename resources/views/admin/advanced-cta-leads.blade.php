@extends('admin.layouts.app')

@section('title', 'CTA - '.$menuLabel)

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-[10px] font-bold uppercase tracking-widest text-primary">CTA / {{ $groupLabel }}</p>
            <h1 class="mt-2 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $menuLabel }}</h1>
            <p class="mt-1 text-sm text-slate-500">Review, search, and manage advanced CTA lead submissions for this menu.</p>
        </div>

        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       id="advanced-cta-lead-search"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search name, email, or phone">
            </div>
            <div class="text-sm text-slate-500" id="advanced-cta-lead-result-summary">Loading leads...</div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Lead documents</p>
                    <p class="mt-1 text-xs text-slate-500" id="advanced-cta-selection-summary">Select rows to download uploaded documents.</p>
                </div>
                <form method="POST" action="{{ route('admin.cta.advanced-leads.documents.download') }}" id="advanced-cta-bulk-download-form">
                    @csrf
                    <input type="hidden" name="menu_type" value="{{ $menuType }}">
                    <div id="advanced-cta-bulk-selected-inputs"></div>
                    <button type="submit"
                            id="download-selected-advanced-documents"
                            class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-[11px] font-bold uppercase tracking-widest text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto"
                            disabled>
                        <span class="material-symbols-outlined !text-[18px]">download</span>
                        Download Documents
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="w-20 px-5 py-4">
                                <label class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                    <input type="checkbox"
                                           id="select-all-advanced-cta-leads"
                                           class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                                    <span>Select</span>
                                </label>
                            </th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Phone</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">City</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Company Name</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="advanced-cta-leads-table-body">
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Loading leads...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500" id="advanced-cta-lead-pagination-summary"></p>
                <div class="flex flex-wrap gap-2" id="advanced-cta-lead-pagination"></div>
            </div>
        </div>
    </div>

    <div id="advanced-cta-detail-modal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4 sm:p-6" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" data-advanced-cta-detail-close="true"></div>
        <div class="relative z-10 w-full max-w-xl rounded-2xl bg-white shadow-[0_30px_90px_rgba(15,23,42,0.28)]" role="dialog" aria-modal="true" aria-labelledby="advanced-cta-detail-title">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                <div>
                    <!-- <p class="text-[10px] font-bold uppercase tracking-widest text-primary">Advanced CTA Lead</p> -->
                    <h2 id="advanced-cta-detail-title" class="mt-1 font-['Space_Grotesk'] text-xl font-bold text-slate-950">Lead Details</h2>
                </div>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" data-advanced-cta-detail-close="true" aria-label="Close details">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto px-5 py-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2" id="advanced-cta-detail-grid"></div>
                <div class="mt-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Message</p>
                    <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-6 text-slate-700" id="advanced-cta-detail-message"></p>
                </div>
                <div class="mt-4 rounded-2xl border border-slate-100 bg-white p-4" id="advanced-cta-detail-document"></div>
            </div>
        </div>
    </div>

    @php
        $advancedCtaLeadRoutes = [
            'data' => route($dataRouteName, ['menuType' => $menuType]),
            'destroy' => route('admin.cta.advanced-leads.destroy', ['advancedCtaLead' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($advancedCtaLeadRoutes);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tableBody = document.getElementById('advanced-cta-leads-table-body');
            const searchInput = document.getElementById('advanced-cta-lead-search');
            const pagination = document.getElementById('advanced-cta-lead-pagination');
            const paginationSummary = document.getElementById('advanced-cta-lead-pagination-summary');
            const resultSummary = document.getElementById('advanced-cta-lead-result-summary');
            const bulkForm = document.getElementById('advanced-cta-bulk-download-form');
            const bulkInputs = document.getElementById('advanced-cta-bulk-selected-inputs');
            const downloadButton = document.getElementById('download-selected-advanced-documents');
            const selectAllBox = document.getElementById('select-all-advanced-cta-leads');
            const selectionSummary = document.getElementById('advanced-cta-selection-summary');
            const detailModal = document.getElementById('advanced-cta-detail-modal');
            const detailGrid = document.getElementById('advanced-cta-detail-grid');
            const detailMessage = document.getElementById('advanced-cta-detail-message');
            const detailDocument = document.getElementById('advanced-cta-detail-document');
            let currentPage = 1;
            let currentSearch = '';
            let searchTimer = null;
            let currentLeads = [];

            const routeFor = (name, id) => routes[name].replace('__ID__', id);
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));

            const requestJson = async (url, options = {}) => {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        ...(options.headers || {}),
                    },
                    ...options,
                });
                const json = await response.json();
                if (!response.ok || json.status === 'error') throw json;
                return json;
            };

            const renderDocument = (lead) => {
                if (!lead.document_url) return '<span class="text-sm text-slate-400">No document</span>';

                return `
                    <a href="${escapeHtml(lead.document_url)}" class="inline-flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs font-semibold text-primary transition hover:bg-primary/10">
                        <span class="material-symbols-outlined !text-[17px]">download</span>
                        <span>${escapeHtml(lead.document_name || 'Download')}</span>
                    </a>
                `;
            };

            const selectedLeadIds = () => Array.from(document.querySelectorAll('.advanced-cta-lead-checkbox:checked')).map((checkbox) => checkbox.value);

            const updateBulkState = () => {
                const checkboxes = Array.from(document.querySelectorAll('.advanced-cta-lead-checkbox'));
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;
                downloadButton.disabled = checkedCount === 0;
                selectionSummary.textContent = checkedCount
                    ? `${checkedCount} selected for document download`
                    : 'Select rows to download uploaded documents.';

                selectAllBox.checked = checkedCount > 0 && checkedCount === checkboxes.length;
                selectAllBox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                selectAllBox.disabled = checkboxes.length === 0;
            };

            const renderDetailField = (label, value) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-1 break-words text-sm font-semibold text-slate-800">${escapeHtml(value || '-')}</p>
                </div>
            `;

            window.openAdvancedCtaLeadDetail = (id) => {
                const lead = currentLeads.find((item) => Number(item.id) === Number(id));
                if (!lead) return;

                detailGrid.innerHTML = [
                    renderDetailField('Name', lead.name),
                    renderDetailField('Email', lead.email),
                    renderDetailField('Phone', lead.phone),
                    renderDetailField('City', lead.city),
                    renderDetailField('Company Name', lead.company_name),
                ].join('');
                detailMessage.textContent = lead.message || '-';
                detailDocument.innerHTML = `
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Uploaded Document</p>
                    <div class="mt-2">${renderDocument(lead)}</div>
                `;
                detailModal.classList.remove('hidden');
                detailModal.classList.add('flex');
                detailModal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
            };

            window.closeAdvancedCtaLeadDetail = () => {
                detailModal.classList.add('hidden');
                detailModal.classList.remove('flex');
                detailModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
            };

            const renderRows = (leads) => {
                if (!leads.length) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">No leads found.</td></tr>';
                    updateBulkState();
                    return;
                }

                tableBody.innerHTML = leads.map((lead) => `
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-5 py-4">
                            <input type="checkbox"
                                   value="${lead.id}"
                                   class="advanced-cta-lead-checkbox h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-900">${escapeHtml(lead.name || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.email || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.phone || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.city || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.company_name || '-')}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-1">
                                <button type="button" class="advanced-cta-action-button text-primary hover:bg-primary/10" title="View" onclick="openAdvancedCtaLeadDetail(${lead.id})"><span class="material-symbols-outlined !text-[20px]">visibility</span></button>
                                <button type="button" class="advanced-cta-action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete" onclick="deleteAdvancedCtaLead(${lead.id})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                updateBulkState();
            };

            const renderPagination = (meta) => {
                paginationSummary.textContent = meta.total
                    ? `Showing ${meta.from} to ${meta.to} of ${meta.total} leads`
                    : 'No leads to show';
                resultSummary.textContent = `${meta.total} lead${meta.total === 1 ? '' : 's'} found`;
                pagination.innerHTML = '';
                if (meta.last_page <= 1) return;

                const addButton = (label, page, disabled = false, active = false) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.disabled = disabled;
                    button.className = `min-w-10 rounded-lg border px-3 py-2 text-sm font-semibold transition ${active ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-600 hover:border-primary hover:text-primary'} ${disabled ? 'cursor-not-allowed opacity-40' : ''}`;
                    button.addEventListener('click', () => loadAdvancedCtaLeads(page));
                    pagination.appendChild(button);
                };

                addButton('Prev', meta.current_page - 1, meta.current_page === 1);
                const start = Math.max(1, meta.current_page - 2);
                const end = Math.min(meta.last_page, meta.current_page + 2);
                for (let page = start; page <= end; page += 1) addButton(String(page), page, false, page === meta.current_page);
                addButton('Next', meta.current_page + 1, meta.current_page === meta.last_page);
            };

            window.loadAdvancedCtaLeads = async (page = 1) => {
                currentPage = page;
                tableBody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Loading leads...</td></tr>';
                try {
                    const url = new URL(routes.data, window.location.origin);
                    url.searchParams.set('page', page);
                    if (currentSearch) url.searchParams.set('search', currentSearch);
                    const json = await requestJson(url);
                    currentLeads = json.data || [];
                    renderRows(json.data);
                    renderPagination(json.meta);
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load advanced CTA leads.')}</td></tr>`;
                    updateBulkState();
                }
            };

            window.deleteAdvancedCtaLead = async (id) => {
                if (!confirm('Delete this advanced CTA lead?')) return;
                try {
                    await requestJson(routeFor('destroy', id), { method: 'DELETE' });
                    await loadAdvancedCtaLeads(currentPage);
                } catch (error) {
                    alert(error.message || 'Unable to delete advanced CTA lead.');
                }
            };

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    loadAdvancedCtaLeads(1);
                }, 250);
            });

            selectAllBox.addEventListener('change', () => {
                document.querySelectorAll('.advanced-cta-lead-checkbox').forEach((checkbox) => {
                    checkbox.checked = selectAllBox.checked;
                });
                updateBulkState();
            });

            tableBody.addEventListener('change', (event) => {
                if (!event.target.closest('.advanced-cta-lead-checkbox')) return;
                updateBulkState();
            });

            bulkForm.addEventListener('submit', (event) => {
                const ids = selectedLeadIds();
                if (!ids.length) {
                    event.preventDefault();
                    alert('Please select at least one advanced CTA lead.');
                    return;
                }

                const selectedWithDocuments = currentLeads.filter((lead) => ids.includes(String(lead.id)) && lead.document_url);
                if (!selectedWithDocuments.length) {
                    event.preventDefault();
                    alert('Selected leads do not have uploaded documents.');
                    return;
                }

                bulkInputs.innerHTML = ids.map((id) => `<input type="hidden" name="advanced_cta_leads[]" value="${escapeHtml(id)}">`).join('');
            });

            detailModal.addEventListener('click', (event) => {
                if (event.target.closest('[data-advanced-cta-detail-close]')) closeAdvancedCtaLeadDetail();
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !detailModal.classList.contains('hidden')) closeAdvancedCtaLeadDetail();
            });

            const style = document.createElement('style');
            style.textContent = '.advanced-cta-action-button{display:inline-flex;height:2.25rem;width:2.25rem;align-items:center;justify-content:center;border-radius:.75rem;transition:all 150ms ease}';
            document.head.appendChild(style);
            loadAdvancedCtaLeads();
        })();
    </script>
@endsection
