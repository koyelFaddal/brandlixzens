@extends('admin.layouts.app')

@section('title', 'CTA - '.$sourceLabel)

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-[10px] font-bold uppercase tracking-widest text-primary">CTA / AI Insight</p>
            <h1 class="mt-2 font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">{{ $sourceLabel }}</h1>
            <p class="mt-1 text-sm text-slate-500">Review, search, and manage CTA lead submissions for this section.</p>
        </div>

        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       id="cta-lead-search"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search name, email, or phone">
            </div>
            <div class="text-sm text-slate-500" id="cta-lead-result-summary">Loading leads...</div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Phone</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">City</th>
                            <!-- <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">CTA Button</th> -->
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Created At</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="cta-leads-table-body">
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Loading leads...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500" id="cta-lead-pagination-summary"></p>
                <div class="flex flex-wrap gap-2" id="cta-lead-pagination"></div>
            </div>
        </div>
    </div>

    @php
        $ctaLeadRoutes = [
            'data' => route('admin.cta.ai-insight.data', ['sourceType' => $sourceType]),
            'destroy' => route('admin.cta.leads.destroy', ['ctaLead' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($ctaLeadRoutes);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tableBody = document.getElementById('cta-leads-table-body');
            const searchInput = document.getElementById('cta-lead-search');
            const pagination = document.getElementById('cta-lead-pagination');
            const paginationSummary = document.getElementById('cta-lead-pagination-summary');
            const resultSummary = document.getElementById('cta-lead-result-summary');
            let currentPage = 1;
            let currentSearch = '';
            let searchTimer = null;

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

            const renderRows = (leads) => {
                if (!leads.length) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">No CTA leads found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = leads.map((lead) => `
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-5 py-4 font-semibold text-slate-900">${escapeHtml(lead.name || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.email || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.phone || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.city || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(lead.created_at || '-')}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end">
                                <button type="button" class="cta-action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete" onclick="deleteCtaLead(${lead.id})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
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
                    button.addEventListener('click', () => loadCtaLeads(page));
                    pagination.appendChild(button);
                };

                addButton('Prev', meta.current_page - 1, meta.current_page === 1);
                const start = Math.max(1, meta.current_page - 2);
                const end = Math.min(meta.last_page, meta.current_page + 2);
                for (let page = start; page <= end; page += 1) addButton(String(page), page, false, page === meta.current_page);
                addButton('Next', meta.current_page + 1, meta.current_page === meta.last_page);
            };

            window.loadCtaLeads = async (page = 1) => {
                currentPage = page;
                tableBody.innerHTML = '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Loading leads...</td></tr>';
                try {
                    const url = new URL(routes.data, window.location.origin);
                    url.searchParams.set('page', page);
                    if (currentSearch) url.searchParams.set('search', currentSearch);
                    const json = await requestJson(url);
                    renderRows(json.data);
                    renderPagination(json.meta);
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load CTA leads.')}</td></tr>`;
                }
            };

            window.deleteCtaLead = async (id) => {
                if (!confirm('Delete this CTA lead?')) return;
                try {
                    await requestJson(routeFor('destroy', id), { method: 'DELETE' });
                    await loadCtaLeads(currentPage);
                } catch (error) {
                    alert(error.message || 'Unable to delete CTA lead.');
                }
            };

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    loadCtaLeads(1);
                }, 250);
            });

            const style = document.createElement('style');
            style.textContent = '.cta-action-button{display:inline-flex;height:2.25rem;width:2.25rem;align-items:center;justify-content:center;border-radius:.75rem;transition:all 150ms ease}';
            document.head.appendChild(style);
            loadCtaLeads();
        })();
    </script>
@endsection
