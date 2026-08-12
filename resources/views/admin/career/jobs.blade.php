@extends('admin.layouts.app')

@section('title', 'Jobs')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Jobs</h1>
                <p class="mt-1 text-sm text-slate-500">Manage career roles by category.</p>
            </div>

            <button type="button"
                    class="admin-primary-button inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg transition"
                    onclick="openJobForm()">
                <span class="material-symbols-outlined !text-[20px]">add</span>
                Add Job
            </button>
        </div>

        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
            <div class="relative w-full md:max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text"
                       id="job-search"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-sm outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                       placeholder="Search by job title">
            </div>
            <div class="text-sm text-slate-500" id="job-result-summary">Loading jobs...</div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Job Title</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Work Location</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Post Date</th>
                            <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="jobs-table-body">
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Loading jobs...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500" id="job-pagination-summary"></p>
                <div class="flex flex-wrap gap-2" id="job-pagination"></div>
            </div>
        </div>
    </div>

    <div class="job-modal-overlay" id="job-form-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('job-form-modal')"></div>
        <div class="job-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Career Job</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl" id="job-form-title">Add Job</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeModal('job-form-modal')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form class="flex min-h-0 flex-1 flex-col" id="job-form" enctype="multipart/form-data">
                <input type="hidden" id="job-id">

                <div class="job-modal-scroll space-y-5 px-5 py-5 sm:px-8">
                    <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-700 hidden" id="job-form-error"></div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <label class="space-y-1">
                        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Job Title</span>
                        <input class="job-input" name="job_title" type="text" required placeholder="AI Engineer">
                        <span class="field-error" data-error-for="job_title"></span>
                    </label>
                    <label class="space-y-1">
                        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category</span>
                        <select class="job-input" name="category" required>
                            <option value="development">Development</option><option value="design">Design</option>
                            <option value="marketing">Marketing</option><option value="customer-service">Customer Service</option>
                            <option value="operations">Operations</option><option value="finance">Finance</option>
                            <option value="management">Management</option>
                        </select>
                        <span class="field-error" data-error-for="category"></span>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <label class="space-y-1"><span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Work Mode</span>
                        <select class="job-input" name="work_mode" required><option>100% Remote</option><option>Hybrid</option><option>On-site</option></select></label>
                    <label class="space-y-1"><span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Location (optional)</span>
                        <input class="job-input" name="work_location" type="text" placeholder="Kolkata, India"></label>
                    <label class="space-y-1"><span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Employment Type</span>
                        <select class="job-input" name="employment_type" required><option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option></select></label>
                </div>

                <label class="space-y-1 block">
                    <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Overview</span>
                    <textarea class="job-input min-h-28" name="overview" required placeholder="Describe the role..."></textarea>
                    <span class="block px-1 text-right text-xs text-slate-400" id="overview-word-count">0 / 45 words</span>
                    <span class="field-error" data-error-for="overview"></span>
                </label>
                <label class="space-y-1">
                    <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Responsibilities</span>
                    <textarea class="job-input min-h-28" name="responsibilities" required placeholder="List the key responsibilities for this role..."></textarea>
                    <span class="field-error" data-error-for="responsibilities"></span>
                </label>

                <label class="space-y-1">
                    <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Experience Required</span>
                    <input class="job-input" name="experience_required" type="text" required placeholder="5+ years">
                    <span class="field-error" data-error-for="experience_required"></span>
                </label>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <label class="space-y-1">
                        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Required Skills</span>
                        <textarea class="job-input min-h-24" name="required_skills" required placeholder="List the required skills for this role..."></textarea>
                        <span class="field-error" data-error-for="required_skills"></span>
                    </label>

                    <label class="space-y-1 block">
                        <span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Preferred Skills</span>
                        <textarea class="job-input min-h-24" name="preferred_skills" required placeholder="List the preferred skills for this role..."></textarea>
                        <span class="field-error" data-error-for="preferred_skills"></span>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_160px] md:items-end">
                    <label class="space-y-1"><span class="ml-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Preview Image</span>
                        <input name="preview_image" type="file" accept="image/png,image/jpeg,image/webp" class="job-input">
                        <span class="field-error" data-error-for="preview_image"></span>
                    </label>
                    <img id="job-image-preview" class="hidden h-28 w-full rounded-xl border border-slate-200 object-cover" alt="Preview image">
                </div>

                </div>

                <div class="shrink-0 border-t border-slate-100 bg-white px-5 py-4 sm:px-8">
                    <button type="submit" class="admin-primary-button flex w-full items-center justify-center gap-2 rounded-xl px-5 py-4 text-sm font-bold uppercase tracking-widest text-white shadow-xl transition" id="job-save-button">
                        <span class="material-symbols-outlined !text-[20px]">save</span>
                        Save Job
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="job-modal-overlay" id="job-view-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal('job-view-modal')"></div>
        <div class="job-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Read Only</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl">Job Details</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeModal('job-view-modal')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="job-modal-scroll flex-1 space-y-5 px-5 py-5 sm:px-8" id="job-view-content"></div>
        </div>
    </div>

    <style>
        .job-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .admin-primary-button { background:#5D5CFF !important; color:#fff !important; }
        .admin-primary-button:hover { background:#4746e8 !important; }

        .job-modal-overlay.is-open {
            display: flex;
        }

        .job-modal-panel {
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

        .job-modal-scroll {
            min-height: 0;
            overflow-y: auto;
        }

        @media (min-width: 640px) {
            .job-modal-overlay {
                padding: 1.5rem;
            }

            .job-modal-panel {
                width: min(920px, calc(100vw - 3rem));
                max-height: calc(100vh - 3rem);
            }
        }

        .job-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid rgb(226 232 240);
            background: rgb(248 250 252);
            padding: 0.75rem;
            font-size: 0.875rem;
            outline: none;
            transition: all 150ms ease;
        }

        .job-input:focus {
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
        $jobRoutes = [
            'data' => route('admin.career.jobs.data'),
            'store' => route('admin.career.jobs.store'),
            'show' => route('admin.career.jobs.show', ['aiJob' => '__ID__']),
            'update' => route('admin.career.jobs.update', ['aiJob' => '__ID__']),
            'destroy' => route('admin.career.jobs.destroy', ['aiJob' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($jobRoutes);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tableBody = document.getElementById('jobs-table-body');
            const searchInput = document.getElementById('job-search');
            const pagination = document.getElementById('job-pagination');
            const paginationSummary = document.getElementById('job-pagination-summary');
            const resultSummary = document.getElementById('job-result-summary');
            const form = document.getElementById('job-form');
            const formError = document.getElementById('job-form-error');
            const imagePreview = document.getElementById('job-image-preview');
            const saveButton = document.getElementById('job-save-button');
            const overviewInput = form.elements.overview;
            const overviewWordCount = document.getElementById('overview-word-count');
            document.body.appendChild(document.getElementById('job-form-modal'));
            document.body.appendChild(document.getElementById('job-view-modal'));
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

            const renderRows = (jobs) => {
                if (!jobs.length) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No jobs found.</td></tr>';
                    return;
                }

                tableBody.innerHTML = jobs.map((job) => `
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">${escapeHtml(job.job_title)}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">${escapeHtml(job.category_label)}</td>
                        <td class="px-5 py-4 text-sm text-slate-600"> ${escapeHtml(job.work_location || '-')}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-slate-800">${escapeHtml(job.job_post_date_readable)}</p>
                          
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <button type="button" class="action-button" title="View" onclick="viewJob(${job.id})"><span class="material-symbols-outlined !text-[20px]">visibility</span></button>
                                <button type="button" class="action-button" title="Edit" onclick="editJob(${job.id})"><span class="material-symbols-outlined !text-[20px]">edit</span></button>
                                <button type="button" class="action-button text-red-500 hover:bg-red-50 hover:text-red-600" title="Delete" onclick="deleteJob(${job.id})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            };

            const renderPagination = (meta) => {
                paginationSummary.textContent = meta.total
                    ? `Showing ${meta.from} to ${meta.to} of ${meta.total} jobs`
                    : 'No jobs to show';

                resultSummary.textContent = `${meta.total} job${meta.total === 1 ? '' : 's'} found`;
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
                    button.addEventListener('click', () => loadJobs(page));
                    pagination.appendChild(button);
                };

                addButton('Prev', meta.current_page - 1, meta.current_page === 1);

                const start = Math.max(1, meta.current_page - 2);
                const end = Math.min(meta.last_page, meta.current_page + 2);

                for (let page = start; page <= end; page += 1) {
                    addButton(String(page), page, false, page === meta.current_page);
                }

                addButton('Next', meta.current_page + 1, meta.current_page === meta.last_page);
            };

            window.loadJobs = async (page = 1) => {
                currentPage = page;
                tableBody.innerHTML = '<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Loading jobs...</td></tr>';

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
                    tableBody.innerHTML = `<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load jobs.')}</td></tr>`;
                }
            };

            window.openJobForm = () => {
                form.reset();
                clearErrors();
                updateOverviewWordCount();
                document.getElementById('job-id').value = '';
                document.getElementById('job-form-title').textContent = 'Add Job';
                imagePreview.classList.add('hidden'); imagePreview.removeAttribute('src');
                openModal('job-form-modal');
            };

            window.openModal = (id) => {
                document.getElementById(id).classList.add('is-open');
                document.body.style.overflow = 'hidden';
            };

            window.closeModal = (id) => {
                document.getElementById(id).classList.remove('is-open');
                document.body.style.overflow = '';
            };

            window.viewJob = async (id) => {
                const content = document.getElementById('job-view-content');
                content.innerHTML = '<p class="py-10 text-center text-sm text-slate-500">Loading job details...</p>';
                openModal('job-view-modal');

                try {
                    const { data: job } = await requestJson(routeFor('show', id));
                    content.innerHTML = `
                        ${job.preview_image_url ? `<img src="${job.preview_image_url}" alt="${escapeHtml(job.job_title)}" class="max-h-72 w-full rounded-2xl object-cover">` : ''}
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-primary">#${job.id}</p>
                            <h3 class="mt-1 font-['Space_Grotesk'] text-2xl font-bold text-slate-950">${escapeHtml(job.job_title)}</h3>
                            <p class="mt-2 text-sm font-semibold text-slate-600">${escapeHtml(job.work_location || '-')}</p>
                            <p class="mt-1 text-sm text-slate-500">${escapeHtml(job.category_label)} · ${escapeHtml(job.work_mode)} · ${escapeHtml(job.employment_type)}</p>
                        </div>
                        ${detailBlock('Overview', job.overview)}
                         ${detailBlock('Responsibilities', job.responsibilities)}
                         <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            ${detailBlock('Required Skills', job.required_skills)}
                            ${detailBlock('Preferred Skills', job.preferred_skills || '-')}
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            ${detailCard('Experience Required', job.experience_required)}
                            ${detailCard('Job Post Date', `${escapeHtml(job.job_post_date_readable)}`, true)}
                        </div>
                    `;
                } catch (error) {
                    content.innerHTML = `<p class="py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load job.')}</p>`;
                }
            };

            window.editJob = async (id) => {
                clearErrors();
                form.reset();
                document.getElementById('job-id').value = id;
                document.getElementById('job-form-title').textContent = 'Edit Job';
                imagePreview.classList.add('hidden'); imagePreview.removeAttribute('src');
                openModal('job-form-modal');

                try {
                    const { data: job } = await requestJson(routeFor('show', id));
                    form.elements.job_title.value = job.job_title || '';
                    form.elements.category.value = job.category || 'development';
                    form.elements.work_mode.value = job.work_mode || '100% Remote';
                    form.elements.work_location.value = job.work_location || '';
                    form.elements.employment_type.value = job.employment_type || 'Full-time';
                    form.elements.overview.value = job.overview || '';
                    updateOverviewWordCount();
                    form.elements.responsibilities.value = job.responsibilities || '';
                    form.elements.required_skills.value = job.required_skills || '';
                    form.elements.preferred_skills.value = job.preferred_skills || '';
                    form.elements.experience_required.value = job.experience_required || '';
                    if (job.preview_image_url) { imagePreview.src = job.preview_image_url; imagePreview.classList.remove('hidden'); }
                } catch (error) {
                    showFormError(error.message || 'Unable to load job for edit.');
                }
            };

            window.deleteJob = async (id) => {
                if (!confirm('Delete this job?')) {
                    return;
                }

                try {
                    await requestJson(routeFor('destroy', id), { method: 'DELETE' });
                    await loadJobs(currentPage);
                } catch (error) {
                    alert(error.message || 'Unable to delete job.');
                }
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearErrors();
                setLoading(true);

                const id = document.getElementById('job-id').value;
                const url = id ? routeFor('update', id) : routes.store;
                const overviewWords = countWords(overviewInput.value);

                if (overviewWords > 45) {
                    showFormError('Overview must not exceed 45 words.');
                    renderFieldErrors({ overview: ['Overview must not exceed 45 words.'] });
                    setLoading(false);
                    return;
                }

                try {
                    await requestJson(url, {
                        method: 'POST',
                        body: new FormData(form),
                    });

                    closeModal('job-form-modal');
                    await loadJobs(id ? currentPage : 1);
                } catch (error) {
                    showFormError(error.message || 'Unable to save job.');
                    renderFieldErrors(error.errors || {});
                } finally {
                    setLoading(false);
                }
            });

            form.elements.preview_image.addEventListener('change', event => {
                const file = event.target.files[0];
                if (!file) return;
                imagePreview.src = URL.createObjectURL(file);
                imagePreview.classList.remove('hidden');
            });

            overviewInput.addEventListener('input', updateOverviewWordCount);

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    loadJobs(1);
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

            function updateOverviewWordCount() {
                const words = countWords(overviewInput.value);
                overviewWordCount.textContent = `${words} / 45 words`;
                overviewWordCount.classList.toggle('text-red-600', words > 45);
                overviewWordCount.classList.toggle('text-slate-400', words <= 45);
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

            loadJobs();
        })();
    </script>
@endsection
