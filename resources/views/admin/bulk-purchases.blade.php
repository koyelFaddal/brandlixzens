@extends('admin.layouts.app')

@section('title', 'Bulk Purchase')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Bulk Purchase</h1>
            <p class="mt-1 text-sm text-slate-500">Review bulk purchase inquiries, selected products, and customer details.</p>
        </div>

        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       id="bulk-purchase-search"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search name, email, phone, or address">
            </div>
            <div class="text-sm text-slate-500" id="bulk-purchase-result-summary">Loading bulk purchases...</div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Name</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Phone</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Products</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Submitted</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="bulk-purchases-table-body">
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Loading bulk purchases...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500" id="bulk-purchase-pagination-summary"></p>
                <div class="flex flex-wrap gap-2" id="bulk-purchase-pagination"></div>
            </div>
        </div>
    </div>

    <div class="bulk-purchase-modal-overlay" id="bulk-purchase-view-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeBulkPurchaseModal('bulk-purchase-view-modal')"></div>
        <div class="bulk-purchase-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Read Only</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl">Bulk Purchase Details</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeBulkPurchaseModal('bulk-purchase-view-modal')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="bulk-purchase-modal-scroll flex-1 space-y-5 px-5 py-5 sm:px-8" id="bulk-purchase-view-content"></div>
        </div>
    </div>

    <style>
        .bulk-purchase-modal-overlay{position:fixed;inset:0;z-index:100;display:none;align-items:center;justify-content:center;padding:1rem}
        .bulk-purchase-modal-overlay.is-open{display:flex}
        .bulk-purchase-modal-panel{position:relative;z-index:10;display:flex;width:min(920px,calc(100vw - 2rem));max-height:calc(100vh - 2rem);flex-direction:column;overflow:hidden;border-radius:1rem;background:white;box-shadow:0 25px 80px -35px rgba(15,23,42,.7)}
        .bulk-purchase-modal-scroll{min-height:0;overflow-y:auto}
        @media (min-width:640px){.bulk-purchase-modal-overlay{padding:1.5rem}.bulk-purchase-modal-panel{width:min(920px,calc(100vw - 3rem));max-height:calc(100vh - 3rem)}}
    </style>

    @php
        $bulkPurchaseRoutes = [
            'data' => route('admin.bulk-purchases.data'),
            'show' => route('admin.bulk-purchases.show', ['bulkPurchase' => '__ID__']),
            'destroy' => route('admin.bulk-purchases.destroy', ['bulkPurchase' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($bulkPurchaseRoutes);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tableBody = document.getElementById('bulk-purchases-table-body');
            const searchInput = document.getElementById('bulk-purchase-search');
            const pagination = document.getElementById('bulk-purchase-pagination');
            const paginationSummary = document.getElementById('bulk-purchase-pagination-summary');
            const resultSummary = document.getElementById('bulk-purchase-result-summary');
            document.body.appendChild(document.getElementById('bulk-purchase-view-modal'));

            let currentPage = 1;
            let currentSearch = '';
            let searchTimer = null;

            const routeFor = (name, id) => routes[name].replace('__ID__', id);
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));

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

            const renderRows = (items) => {
                if (!items.length) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">No bulk purchase inquiries found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = items.map((item) => `
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-5 py-4 font-semibold text-slate-900">${escapeHtml(item.full_name || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(item.email || '-')}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(item.phone || '-')}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-slate-700">${escapeHtml(item.products_count)} product${item.products_count === 1 ? '' : 's'}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(item.submitted_date || '-')}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" class="bulk-purchase-action-button" title="View" onclick="viewBulkPurchase(${item.id})"><span class="material-symbols-outlined !text-[20px]">visibility</span></button>
                                <button type="button" class="bulk-purchase-action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete" onclick="deleteBulkPurchase(${item.id})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            };

            const renderPagination = (meta) => {
                paginationSummary.textContent = meta.total ? `Showing ${meta.from} to ${meta.to} of ${meta.total} inquiries` : 'No inquiries to show';
                resultSummary.textContent = `${meta.total} bulk purchase${meta.total === 1 ? '' : 's'} found`;
                pagination.innerHTML = '';
                if (meta.last_page <= 1) return;

                const addButton = (label, page, disabled = false, active = false) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.disabled = disabled;
                    button.className = `min-w-10 rounded-lg border px-3 py-2 text-sm font-semibold transition ${active ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-600 hover:border-primary hover:text-primary'} ${disabled ? 'cursor-not-allowed opacity-40' : ''}`;
                    button.addEventListener('click', () => loadBulkPurchases(page));
                    pagination.appendChild(button);
                };

                addButton('Prev', meta.current_page - 1, meta.current_page === 1);
                for (let page = Math.max(1, meta.current_page - 2); page <= Math.min(meta.last_page, meta.current_page + 2); page += 1) addButton(String(page), page, false, page === meta.current_page);
                addButton('Next', meta.current_page + 1, meta.current_page === meta.last_page);
            };

            window.loadBulkPurchases = async (page = 1) => {
                currentPage = page;
                tableBody.innerHTML = '<tr><td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Loading bulk purchases...</td></tr>';
                try {
                    const url = new URL(routes.data, window.location.origin);
                    url.searchParams.set('page', page);
                    if (currentSearch) url.searchParams.set('search', currentSearch);
                    const json = await requestJson(url);
                    renderRows(json.data);
                    renderPagination(json.meta);
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="6" class="px-5 py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load bulk purchases.')}</td></tr>`;
                }
            };

            window.openBulkPurchaseModal = (id) => {
                document.getElementById(id).classList.add('is-open');
                document.body.style.overflow = 'hidden';
            };

            window.closeBulkPurchaseModal = (id) => {
                document.getElementById(id).classList.remove('is-open');
                document.body.style.overflow = '';
            };

            const detailBlock = (label, value) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-3 whitespace-pre-line break-words text-sm leading-6 text-slate-600">${escapeHtml(value || '-')}</p>
                </div>
            `;

            window.viewBulkPurchase = async (id) => {
                const content = document.getElementById('bulk-purchase-view-content');
                content.innerHTML = '<p class="py-10 text-center text-sm text-slate-500">Loading bulk purchase details...</p>';
                openBulkPurchaseModal('bulk-purchase-view-modal');
                try {
                    const { data: item } = await requestJson(routeFor('show', id));
                    const products = Array.isArray(item.products) ? item.products : [];
                    content.innerHTML = `
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-primary">#${item.id}</p>
                            <h3 class="mt-1 font-['Space_Grotesk'] text-2xl font-bold text-slate-950">${escapeHtml(item.full_name || '-')}</h3>
                            <p class="mt-2 text-sm font-semibold text-slate-600">${escapeHtml(item.submitted_at || '-')}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            ${detailBlock('Email', item.email)}
                            ${detailBlock('Phone', item.phone)}
                            ${detailBlock('Gender', item.gender)}
                            ${detailBlock('Address', item.address)}
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Selected Products</p>
                            <div class="mt-4 grid gap-3">
                                ${products.length ? products.map((product) => `
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                        <p class="font-semibold text-slate-900">${escapeHtml(product.name || 'Product')}</p>
                                        <p class="mt-1 text-xs text-slate-500">${escapeHtml(product.sku || 'No SKU')} ${product.price ? `- Rs. ${escapeHtml(product.price)}` : ''}</p>
                                    </div>
                                `).join('') : '<p class="text-sm text-slate-500">No products selected.</p>'}
                            </div>
                        </div>
                    `;
                } catch (error) {
                    content.innerHTML = `<p class="py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load bulk purchase.')}</p>`;
                }
            };

            window.deleteBulkPurchase = async (id) => {
                if (!confirm('Delete this bulk purchase inquiry?')) return;
                try {
                    await requestJson(routeFor('destroy', id), { method: 'DELETE' });
                    await loadBulkPurchases(currentPage);
                } catch (error) {
                    alert(error.message || 'Unable to delete bulk purchase.');
                }
            };

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    loadBulkPurchases(1);
                }, 250);
            });

            const style = document.createElement('style');
            style.textContent = '.bulk-purchase-action-button{display:inline-flex;height:2.25rem;width:2.25rem;align-items:center;justify-content:center;border-radius:.75rem;color:rgb(100 116 139);transition:all 150ms ease}.bulk-purchase-action-button:hover{background:rgba(93,92,255,.08);color:#5D5CFF}';
            document.head.appendChild(style);

            loadBulkPurchases();
        })();
    </script>
@endsection
