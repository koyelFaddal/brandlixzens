@extends('admin.layouts.app')

@section('title', 'Applications')

@section('content')
    <div class="w-full max-w-7xl mx-auto px-4 pb-20 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="font-['Space_Grotesk'] text-3xl font-bold tracking-tight text-slate-950">Applications</h1>
                <p class="mt-1 text-sm text-slate-500">Filter applications by job, future openings, and candidate details.</p>
            </div>
        </div>

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-sm font-semibold text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-5 rounded-[1.25rem] border border-slate-100 bg-white p-3 shadow-sm shadow-slate-200/50 sm:p-4">
            <form method="GET" action="{{ route('admin.career.applications') }}" id="application-filter-form" class="space-y-3">
                <div class="max-w-3xl">
                    <label for="application-filter" class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Position</label>
                    <select id="application-filter"
                            name="filter"
                            class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-800 outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20">
                        <option value="all" @selected($selectedFilter === 'all')>All Applications</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job->id }}" @selected((string) $selectedFilter === (string) $job->id)>
                                {{ $job->job_title }}
                            </option>
                        @endforeach
                        <option value="future_opening" @selected($selectedFilter === 'future_opening')>Future Opening</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_auto_auto] lg:items-center">
                    <div class="relative w-full">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               id="application-search"
                               class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20"
                               placeholder="Search name, city, major experience, salary, or notice period">
                    </div>
                    <button type="submit"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-primary px-4 text-[11px] font-bold uppercase tracking-widest text-white transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30">
                        <span class="material-symbols-outlined !text-[16px]">search</span>
                        Search
                    </button>
                    <a href="{{ route('admin.career.applications') }}"
                       class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 px-4 text-[11px] font-bold uppercase tracking-widest text-slate-500 transition hover:border-primary hover:text-primary">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.career.applications.resumes.download') }}" id="bulk-resume-form">
            @csrf

            <div class="overflow-hidden rounded-[1.25rem] border border-slate-100 bg-white shadow-sm shadow-slate-200/50">
                <div class="flex flex-col gap-4 border-b border-slate-100 px-4 py-4 sm:px-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-base font-bold text-slate-900">
                            {{ $applications->total() }} application{{ $applications->total() === 1 ? '' : 's' }} found
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <label class="inline-flex h-12 items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 text-xs font-bold uppercase tracking-widest text-slate-500 lg:hidden">
                            <input type="checkbox"
                                   class="select-all-applications h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                            Select All
                        </label>
                        <button type="submit"
                                id="download-selected-resumes"
                            class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-[11px] font-bold uppercase tracking-widest text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto"
                            disabled>
                        <span class="material-symbols-outlined !text-[18px]">download</span>
                        Download Resume
                    </button>
                    </div>
                </div>

                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full min-w-[1040px] table-fixed text-left">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="w-20 px-5 py-4">
                                    <label class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                        <input type="checkbox"
                                               class="select-all-applications h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                                        <span>Select</span>
                                    </label>
                                </th>
                                <th class="w-[16%] px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Name</th>
                                <th class="w-[12%] px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">City</th>
                                <th class="w-[18%] px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Major Experience</th>
                                <th class="w-[14%] px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Experience</th>
                                <th class="w-[13%] px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Salary</th>
                                <th class="w-[15%] px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Notice Period</th>
                                <th class="w-[12%] px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($applications as $application)
                                <tr class="transition hover:bg-slate-50/80">
                                    <td class="px-5 py-4">
                                        <input type="checkbox"
                                               name="applications[]"
                                               value="{{ $application->id }}"
                                               class="application-checkbox h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="break-words font-semibold text-slate-900">{{ $application->full_name }}</p>
                                    </td>
                                    <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $application->city ?: '-' }}</td>
                                    <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $application->major_experience ?: '-' }}</td>
                                    <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $application->years_of_experience ?: '-' }}</td>
                                    <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $application->present_salary ?: '-' }}</td>
                                    <td class="break-words px-5 py-4 text-sm text-slate-600">{{ $application->notice_period ?: '-' }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="application-action-button" title="View" onclick="viewApplication({{ $application->id }})"><span class="material-symbols-outlined !text-[20px]">visibility</span></button>
                                            <!-- @if ($application->resume_file_path)
                                                <a href="{{ route('admin.career.applications.resume', $application) }}" class="application-action-button" title="Download PDF / CV"><span class="material-symbols-outlined !text-[20px]">download</span></a>
                                            @endif -->
                                            <button type="button" class="application-action-button application-delete-button text-red-500" title="Delete" onclick="deleteApplication({{ $application->id }})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">No applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-slate-100 lg:hidden">
                    @forelse ($applications as $application)
                        <div class="p-4 sm:p-5">
                            <div class="mb-4 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="break-words text-base font-bold text-slate-900">{{ $application->full_name }}</p>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-widest text-slate-400">{{ $application->city ?: 'City not provided' }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <button type="button" class="application-action-button" title="View" onclick="viewApplication({{ $application->id }})"><span class="material-symbols-outlined !text-[20px]">visibility</span></button>
                                    @if ($application->resume_file_path)
                                        <a href="{{ route('admin.career.applications.resume', $application) }}" class="application-action-button" title="Download PDF / CV"><span class="material-symbols-outlined !text-[20px]">download</span></a>
                                    @endif
                                    <button type="button" class="application-action-button application-delete-button text-red-500" title="Delete" onclick="deleteApplication({{ $application->id }})"><span class="material-symbols-outlined !text-[20px]">delete</span></button>
                                    <input type="checkbox"
                                           name="applications[]"
                                           value="{{ $application->id }}"
                                           class="application-checkbox h-5 w-5 rounded border-slate-300 text-primary focus:ring-primary">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Major Experience</p>
                                    <p class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $application->major_experience ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Years of Experience</p>
                                    <p class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $application->years_of_experience ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Salary</p>
                                    <p class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $application->present_salary ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Notice Period</p>
                                    <p class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $application->notice_period ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-500">No applications found.</div>
                    @endforelse
                </div>

                <div class="flex flex-col gap-4 border-t border-slate-100 px-4 py-4 sm:px-5 xl:flex-row xl:items-center xl:justify-between">
                    <p class="text-sm text-slate-500">
                        Page {{ $applications->currentPage() }} of {{ $applications->lastPage() }}
                    </p>
                    <div class="application-pagination">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="application-modal-overlay" id="application-view-modal">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeApplicationModal('application-view-modal')"></div>
        <div class="application-modal-panel">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-8 sm:py-6">
                <div>
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">Read Only</p>
                    <h2 class="font-['Space_Grotesk'] text-xl font-bold text-slate-950 sm:text-2xl">Application Details</h2>
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" onclick="closeApplicationModal('application-view-modal')">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="application-modal-scroll flex-1 space-y-5 px-5 py-5 sm:px-8" id="application-view-content"></div>
        </div>
    </div>

    <style>
        .application-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .application-modal-overlay.is-open {
            display: flex;
        }

        .application-modal-panel {
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

        .application-modal-scroll {
            min-height: 0;
            overflow-y: auto;
        }

        .application-action-button {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            color: rgb(100 116 139);
            transition: all 150ms ease;
        }

        .application-action-button:hover {
            background: rgba(93, 92, 255, .08);
            color: #5D5CFF;
        }

      
    </style>

    @php
        $applicationRoutes = [
            'show' => route('admin.career.applications.show', ['jobApplication' => '__ID__']),
            'resume' => route('admin.career.applications.resume', ['jobApplication' => '__ID__']),
            'destroy' => route('admin.career.applications.destroy', ['jobApplication' => '__ID__']),
        ];
    @endphp

    <script>
        (() => {
            const routes = @json($applicationRoutes);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const filterForm = document.getElementById('application-filter-form');
            const filterSelect = document.getElementById('application-filter');
            const searchInput = document.getElementById('application-search');
            const selectAllBoxes = Array.from(document.querySelectorAll('.select-all-applications'));
            const checkboxes = Array.from(document.querySelectorAll('.application-checkbox'));
            const downloadButton = document.getElementById('download-selected-resumes');
            const bulkForm = document.getElementById('bulk-resume-form');
            let searchTimer = null;

            const visibleCheckboxes = () => checkboxes.filter((checkbox) => checkbox.offsetParent !== null);

            const updateBulkState = () => {
                const visible = visibleCheckboxes();
                const checkedCount = visible.filter((checkbox) => checkbox.checked).length;
                downloadButton.disabled = checkedCount === 0;

                selectAllBoxes.forEach((selectAll) => {
                    selectAll.checked = checkedCount > 0 && checkedCount === visible.length;
                    selectAll.indeterminate = checkedCount > 0 && checkedCount < visible.length;
                });
            };

            filterSelect?.addEventListener('change', () => {
                filterForm?.submit();
            });

            searchInput?.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    filterForm?.submit();
                }, 500);
            });

            selectAllBoxes.forEach((selectAll) => {
                selectAll.addEventListener('change', () => {
                    visibleCheckboxes().forEach((checkbox) => {
                        checkbox.checked = selectAll.checked;
                    });
                    updateBulkState();
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateBulkState);
            });

            bulkForm?.addEventListener('submit', (event) => {
                if (checkboxes.some((checkbox) => checkbox.checked)) return;

                event.preventDefault();
                alert('Please select at least one application.');
            });

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
            const detailCard = (label, value) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-4">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-2 break-words text-sm font-semibold leading-6 text-slate-800">${escapeHtml(value || '-')}</p>
                </div>
            `;
            const detailBlock = (label, value) => `
                <div class="rounded-2xl border border-slate-100 bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">${escapeHtml(label)}</p>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">${escapeHtml(value || '-')}</p>
                </div>
            `;

            window.openApplicationModal = (id) => {
                document.getElementById(id).classList.add('is-open');
                document.body.style.overflow = 'hidden';
            };

            window.closeApplicationModal = (id) => {
                document.getElementById(id).classList.remove('is-open');
                document.body.style.overflow = '';
            };

            window.viewApplication = async (id) => {
                const content = document.getElementById('application-view-content');
                content.innerHTML = '<p class="py-10 text-center text-sm text-slate-500">Loading application details...</p>';
                openApplicationModal('application-view-modal');

                try {
                    const { data: application } = await requestJson(routeFor('show', id));
                    const isFuture = application.application_type === 'future_opening';
                    content.innerHTML = `
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-primary">#${application.id}</p>
                            <h3 class="mt-2 font-['Space_Grotesk'] text-2xl font-bold text-slate-950">${escapeHtml(application.full_name)}</h3>
                            <p class="mt-2 text-sm font-semibold text-slate-600">${escapeHtml(application.job_title)}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            ${detailCard('Email', application.email)}
                            ${detailCard('Phone', application.phone_number)}
                            ${detailCard('City', application.city || '-')}
                            ${detailCard('Major Experience', application.major_experience || '-')}
                            ${detailCard('Years of Experience', application.years_of_experience || '-')}
                            ${detailCard('Present Salary', application.present_salary || '-')}
                            ${detailCard('Notice Period', application.notice_period || '-')}
                            ${detailCard('LinkedIn Profile', application.linkedin_url || '-')}
                            ${detailCard('GitHub Profile', application.github_url || '-')}
                            ${detailCard('Portfolio', application.portfolio_url || '-')}
                            ${detailCard('Expected CTC', application.expected_salary || '-')}
                        </div>
                        ${detailBlock('Current Role', application.current_role || '-')}
                        ${detailBlock('Skills', application.skills || '-')}
                        ${detailBlock('About Yourself', application.ai_challenge || '-')}
                        <div class="rounded-2xl border border-slate-100 bg-white p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Resume</p>
                            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm font-semibold text-slate-800">${escapeHtml(application.resume_file_name || '-')}</p>
                                ${application.has_resume ? `<a style="display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:12px;background:#5b55f7;padding:12px 18px;color:#fff;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none" href="${escapeHtml(application.resume_url)}"><span class="material-symbols-outlined" style="font-size:18px">download</span> Download PDF / CV</a>` : '<span class="text-sm text-red-500">Resume file missing</span>'}
                            </div>
                        </div>
                    `;
                } catch (error) {
                    content.innerHTML = `<p class="py-10 text-center text-sm text-red-600">${escapeHtml(error.message || 'Unable to load application.')}</p>`;
                }
            };

            window.deleteApplication = async (id) => {
                if (!confirm('Delete this application? This will also delete the uploaded resume file.')) {
                    return;
                }

                try {
                    await requestJson(routeFor('destroy', id), { method: 'DELETE' });
                    window.location.reload();
                } catch (error) {
                    alert(error.message || 'Unable to delete application.');
                }
            };

            updateBulkState();
        })();
    </script>
@endsection
