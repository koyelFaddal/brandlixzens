@extends('admin.layouts.app')

@section('title', 'Resources')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        {{-- Page heading and primary create button. --}}
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Resources</h1>
                <p class="mt-1 text-sm text-slate-500">Manage AI resources with title search, uploads, and server pagination.</p>
            </div>

            <button type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0"
                    onclick="openResourceForm()">
                <span class="material-symbols-outlined !text-[20px]">add</span>
                Add New
            </button>
        </div>

        {{-- Search bar. The request is debounced in JavaScript for fast typing. --}}
        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       id="resource-search"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search by resource title">
            </div>
            <div class="text-sm text-slate-500" id="resource-result-summary">Loading resources...</div>
        </div>

        {{-- Resources table. Full text is kept out of the table for light rendering. --}}
        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">ID</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Title</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Date</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="resources-table-body">
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Loading resources...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination is rendered from Laravel paginate() metadata. --}}
            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500" id="resource-pagination-summary"></p>
                <div class="flex flex-wrap gap-2" id="resource-pagination"></div>
            </div>
        </div>
    </div>

    {{-- Add/Edit modal. Same modal shell and button styling as the existing jobs page. --}}
    <div class="resource-modal-overlay" id="resource-form-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('resource-form-modal')"></div>
        <div class="resource-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">AI Resource</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl" id="resource-form-title">Add Resource</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeModal('resource-form-modal')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form class="flex min-h-0 flex-1 flex-col" id="resource-form" enctype="multipart/form-data">
                <input type="hidden" id="resource-id">

                <div class="resource-modal-scroll space-y-5 px-5 py-5 sm:px-8">
                    <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700 hidden" id="resource-form-error"></div>

                    <label class="space-y-1 block">
                        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Title</span>
                        <input class="resource-input" name="title" type="text" required placeholder="Resource title">
                        <span class="field-error" data-error-for="title"></span>
                    </label>

                    <label class="space-y-1 block">
                        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</span>
                        <textarea class="resource-input min-h-32" name="description" required placeholder="Write resource description..."></textarea>
                        <span class="block px-1 text-right text-xs text-slate-400" id="description-word-count">0 / 35 words</span>
                        <span class="field-error" data-error-for="description"></span>
                    </label>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <label class="space-y-1">
                            <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Preview Image</span>
                            <input class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-primary hover:file:bg-primary/20" name="preview_image" type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif">
                            <span class="block px-1 text-xs text-slate-400" id="resource-current-preview"></span>
                            <span class="field-error" data-error-for="preview_image"></span>
                        </label>

                        <label class="space-y-1">
                            <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Resource File</span>
                            <input class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-primary hover:file:bg-primary/20" name="file" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            <span class="block px-1 text-xs text-slate-400" id="resource-current-file"></span>
                            <span class="field-error" data-error-for="file"></span>
                        </label>
                    </div>
                </div>

                <div class="shrink-0 border-t border-slate-100 bg-white px-5 py-4 sm:px-8">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-4 text-sm font-bold uppercase tracking-widest text-white shadow-xl shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-2xl active:translate-y-0" id="resource-save-button">
                        <span class="material-symbols-outlined !text-[20px]">save</span>
                        Save Resource
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Read-only details modal with download link. --}}
    <div class="resource-modal-overlay" id="resource-view-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('resource-view-modal')"></div>
        <div class="resource-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Read Only</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl">Resource Details</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeModal('resource-view-modal')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="resource-modal-scroll flex-1 space-y-5 px-5 py-5 sm:px-8" id="resource-view-content"></div>
        </div>
    </div>

    <style>
        .resource-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .resource-modal-overlay.is-open {
            display: flex;
        }

        .resource-modal-panel {
            position: relative;
            z-index: 10;
            display: flex;
            width: min(920px, calc(100vw - 2rem));
            max-height: calc(100vh - 2rem);
            flex-direction: column;
            overflow: hidden;
            border-radius: 1rem;
            background: white;
            box-shadow: 0 25px 80px -35px rgba(15, 23, 42, 0.7);
        }

        .resource-modal-scroll {
            min-height: 0;
            overflow-y: auto;
        }

        @media (min-width: 640px) {
            .resource-modal-overlay {
                padding: 1.5rem;
            }

            .resource-modal-panel {
                width: min(920px, calc(100vw - 3rem));
                max-height: calc(100vh - 3rem);
            }
        }

        .resource-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid rgb(226 232 240);
            background: rgb(248 250 252);
            padding: 0.75rem;
            font-size: 0.875rem;
            outline: none;
            transition: all 150ms ease;
        }

        .resource-input:focus {
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

    @php
        // Route names are centralized here so JavaScript stays simple and readable.
        $resourceRoutes = [
            'data' => route('admin.resources.data'),
            'store' => route('admin.resources.store'),
            'show' => route('admin.resources.show', ['aiResource' => '__ID__']),
            'update' => route('admin.resources.update', ['aiResource' => '__ID__']),
            'destroy' => route('admin.resources.destroy', ['aiResource' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($resourceRoutes);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tableBody = document.getElementById('resources-table-body');
            const searchInput = document.getElementById('resource-search');
            const pagination = document.getElementById('resource-pagination');
            const paginationSummary = document.getElementById('resource-pagination-summary');
            const resultSummary = document.getElementById('resource-result-summary');
            const form = document.getElementById('resource-form');
            const formError = document.getElementById('resource-form-error');
            const descriptionInput = form.elements.description;
            const descriptionWordCount = document.getElementById('description-word-count');
            const currentFile = document.getElementById('resource-current-file');
            const currentPreview = document.getElementById('resource-current-preview');
            const saveButton = document.getElementById('resource-save-button');

            document.body.appendChild(document.getElementById('resource-form-modal'));
            document.body.appendChild(document.getElementById('resource-view-modal'));

            let currentPage = 1;
            let currentSearch = '';
            let searchTimer = null;

            const routeFor = (name, id) => routes[name].replace('__ID__', id);

            // Escape dynamic text before inserting it into table or modal HTML.
            const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[char]));

            // Shared JSON request helper for all CRUD actions.
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

                if (!response.ok || json.status === 'error') {
                    throw json;
                }

                return json;
            };

            const setLoading = (isLoading) => {
                saveButton.disabled = isLoading;
                saveButton.classList.toggle('opacity-60', isLoading);
                saveButton.classList.toggle('cursor-not-allowed', isLoading);
            };

            // Render only short previews in the table; full content lives in the view modal.
            const renderRows = (resources) => {
                if (!resources.length) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No resources found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = resources.map((resource) => `
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">#${resource.id}</td>
                        <td class="px-5 py-4">
                            <p class="max-w-xs truncate font-semibold text-slate-900" title="${escapeHtml(resource.title)}">${escapeHtml(resource.title_short)}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="max-w-md truncate text-sm text-slate-600" title="${escapeHtml(resource.description)}">${escapeHtml(resource.description_short)}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-slate-800">${escapeHtml(resource.post_date_readable)}</p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" class="action-button" title="View" onclick="viewResource(${resource.id})"><span class="material-symbols-outlined !text-[20px]">visibility</span></button>
                                <button type="button" class="action-button" title="Edit" onclick="editResource(${resource.id})"><span class="material-symbols-outlined !text-[20px]">edit</span></button>
                                <button type="button" class="action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete" onclick="deleteResource(${resource.id})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            };

            // Previous/Next plus nearby page numbers from Laravel pagination metadata.
            const renderPagination = (meta) => {
                paginationSummary.textContent = meta.total
                    ? `Showing ${meta.from} to ${meta.to} of ${meta.total} resources`
                    : 'No resources to show';

                resultSummary.textContent = `${meta.total} resource${meta.total === 1 ? '' : 's'} found`;
                pagination.innerHTML = '';

                if (meta.last_page <= 1) {
                    return;
                }

                const addButton = (label, page, disabled = false, active = false) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.disabled = disabled;
                    button.className = `min-w-10 rounded-lg border px-3 py-2 text-sm font-semibold transition ${active ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-600 hover:border-primary hover:text-primary'} ${disabled ? 'cursor-not-allowed opacity-40' : ''}`;
                    button.addEventListener('click', () => loadResources(page));
                    pagination.appendChild(button);
                };

                addButton('Previous', meta.current_page - 1, meta.current_page === 1);

                const start = Math.max(1, meta.current_page - 2);
                const end = Math.min(meta.last_page, meta.current_page + 2);

                for (let page = start; page <= end; page += 1) {
                    addButton(String(page), page, false, page === meta.current_page);
                }

                addButton('Next', meta.current_page + 1, meta.current_page === meta.last_page);
            };

            // Load one paginated page of resources from the controller.
            window.loadResources = async (page = 1) => {
                currentPage = page;
                tableBody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Loading resources...</td></tr>';

                try {
                    const url = new URL(routes.data, window.location.origin);
                    url.searchParams.set('page', page);
                    if (currentSearch) {
                        url.searchParams.set('search', currentSearch);
                    }

                    const json = await requestJson(url);
                    renderRows(json.data);
                    renderPagination(json.meta);
                } catch (error) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load resources.')}</td></tr>`;
                }
            };

            // Prepare the modal for creating a new resource.
            window.openResourceForm = () => {
                form.reset();
                clearErrors();
                updateDescriptionWordCount();
                document.getElementById('resource-id').value = '';
                document.getElementById('resource-form-title').textContent = 'Add Resource';
                currentFile.textContent = '';
                openModal('resource-form-modal');
            };

            window.openModal = (id) => {
                document.getElementById(id).classList.add('is-open');
                document.body.style.overflow = 'hidden';
            };

            window.closeModal = (id) => {
                document.getElementById(id).classList.remove('is-open');
                document.body.style.overflow = '';
            };

            // Show the complete resource details without editable fields.
            window.viewResource = async (id) => {
                const content = document.getElementById('resource-view-content');
                content.innerHTML = '<p class="py-10 text-center text-sm text-slate-500">Loading resource details...</p>';
                openModal('resource-view-modal');

                try {
                    const { data: resource } = await requestJson(routeFor('show', id));
                    content.innerHTML = `
                        ${resource.preview_image_url ? `<img src="${resource.preview_image_url}" loading="lazy" alt="${escapeHtml(resource.title)}" class="max-h-80 w-full rounded-2xl border border-slate-100 object-cover">` : ''}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-primary">#${resource.id}</p>
                            <h3 class="mt-1 font-['Space_Grotesk'] text-2xl font-bold text-slate-950">${escapeHtml(resource.title)}</h3>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            ${detailCard('Post Date', `${escapeHtml(resource.post_date_readable)}<br><span class="font-mono text-xs text-slate-400">${escapeHtml(resource.post_date)}</span>`, true)}
                            ${detailCard('Resource File', resource.file_name || '-')}
                            ${detailCard('File Size', resource.file_size_readable || '-')}
                            ${detailCard('File Type', resource.file_extension ? resource.file_extension.toUpperCase() : '-')}
                        </div>
                        ${detailBlock('Description', resource.description)}
                        ${resource.file_url ? `<a href="${resource.file_url}" download class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0"><span class="material-symbols-outlined !text-[20px]">download</span>Download File</a>` : ''}
                    `;
                } catch (error) {
                    content.innerHTML = `<p class="py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load resource.')}</p>`;
                }
            };

            // Load the resource and fill the edit form with current values.
            window.editResource = async (id) => {
                clearErrors();
                form.reset();
                document.getElementById('resource-id').value = id;
                document.getElementById('resource-form-title').textContent = 'Edit Resource';
                currentFile.textContent = '';
                openModal('resource-form-modal');

                try {
                    const { data: resource } = await requestJson(routeFor('show', id));
                    form.elements.title.value = resource.title || '';
                    form.elements.description.value = resource.description || '';
                    updateDescriptionWordCount();
                    currentFile.textContent = resource.file_name ? `Current resource file: ${resource.file_name}` : '';
                    currentPreview.textContent = resource.preview_image_url ? 'Current preview image uploaded' : '';
                } catch (error) {
                    showFormError(error.message || 'Unable to load resource for edit.');
                }
            };

            // Delete resource row and uploaded file through the controller.
            window.deleteResource = async (id) => {
                if (!confirm('Delete this resource? This will also delete its resource file and preview image.')) {
                    return;
                }

                try {
                    await requestJson(routeFor('destroy', id), { method: 'DELETE' });
                    await loadResources(currentPage);
                } catch (error) {
                    alert(error.message || 'Unable to delete resource.');
                }
            };

            // Create/update form submission using FormData for file uploads.
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearErrors();
                setLoading(true);

                const id = document.getElementById('resource-id').value;
                const url = id ? routeFor('update', id) : routes.store;
                const descriptionWords = countWords(descriptionInput.value);

                if (descriptionWords > 35) {
                    showFormError('Description must not exceed 35 words.');
                    renderFieldErrors({ description: ['Description must not exceed 35 words.'] });
                    setLoading(false);
                    return;
                }

                try {
                    await requestJson(url, {
                        method: 'POST',
                        body: new FormData(form),
                    });

                    closeModal('resource-form-modal');
                    await loadResources(id ? currentPage : 1);
                } catch (error) {
                    showFormError(error.message || 'Unable to save resource.');
                    renderFieldErrors(error.errors || {});
                } finally {
                    setLoading(false);
                }
            });

            descriptionInput.addEventListener('input', updateDescriptionWordCount);

            // Title-only search with a small debounce to avoid extra requests.
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    loadResources(1);
                }, 250);
            });

            const clearErrors = () => {
                formError.classList.add('hidden');
                formError.textContent = '';
                document.querySelectorAll('[data-error-for]').forEach((element) => {
                    element.textContent = '';
                });
            };

            const showFormError = (message) => {
                formError.textContent = message;
                formError.classList.remove('hidden');
            };

            const renderFieldErrors = (errors) => {
                Object.entries(errors).forEach(([field, messages]) => {
                    const target = document.querySelector(`[data-error-for="${field}"]`);
                    if (target) {
                        target.textContent = messages[0] || '';
                    }
                });
            };

            function countWords(value) {
                return value.trim().split(/\s+/).filter(Boolean).length;
            }

            function updateDescriptionWordCount() {
                const words = countWords(descriptionInput.value);
                descriptionWordCount.textContent = `${words} / 35 words`;
                descriptionWordCount.classList.toggle('text-red-600', words > 35);
                descriptionWordCount.classList.toggle('text-slate-400', words <= 35);
            }
            const detailCard = (label, value, raw = false) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-800">${raw ? value : escapeHtml(value || '-')}</p>
                </div>
            `;

            const detailBlock = (label, value) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">${escapeHtml(value || '-')}</p>
                </div>
            `;

            const style = document.createElement('style');
            style.textContent = '.action-button{display:inline-flex;height:2.25rem;width:2.25rem;align-items:center;justify-content:center;border-radius:.75rem;color:rgb(100 116 139);transition:all 150ms ease}.action-button:hover{background:rgba(93,92,255,.08);color:#5D5CFF}';
            document.head.appendChild(style);

            loadResources();
        })();
    </script>
@endsection

