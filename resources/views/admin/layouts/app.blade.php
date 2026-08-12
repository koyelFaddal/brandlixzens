<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,0&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            line-height: 1;
            display: inline-block;
            text-transform: none;
            letter-spacing: normal;
            white-space: nowrap;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }

        .submenu-hidden {
            display: none;
        }

        .submenu-open {
            display: block;
        }
        .fl-brand {
            display: flex;
            align-items: center;
            min-width: 360px;
            text-decoration: none;
        }
        .fl-brand > span {
            margin-top: 35px;
        }
        .fl-brand-name {
            display: block;
            font-family: "Playfair Display", Georgia, serif;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            color: #8f3a1b;
            margin-left: 43px;
        }
        .fl-brand-sub {
            display: block;
            margin-top: 4px;
            color: #8b4b25;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-left: 52px;
        }

        button[title="Delete"],
        a[title="Delete"],
        .action-button[title="Delete"],
        .customer-action-button[title="Delete"],
        .dealer-action-button[title="Delete"],
        .application-action-button[title="Delete"],
        .bulk-purchase-action-button[title="Delete"],
        .advanced-cta-action-button[title="Delete"],
        .cta-action-button[title="Delete"],
        .contact-action-button.is-delete {
            border: 1px solid rgb(254 202 202) !important;
            background: rgb(254 242 242) !important;
            color: rgb(220 38 38) !important;
        }

        button[title="Delete"]:hover,
        a[title="Delete"]:hover,
        .action-button[title="Delete"]:hover,
        .customer-action-button[title="Delete"]:hover,
        .dealer-action-button[title="Delete"]:hover,
        .application-action-button[title="Delete"]:hover,
        .bulk-purchase-action-button[title="Delete"]:hover,
        .advanced-cta-action-button[title="Delete"]:hover,
        .cta-action-button[title="Delete"]:hover,
        .contact-action-button.is-delete:hover {
            border-color: rgb(248 113 113) !important;
            background: rgb(254 226 226) !important;
            color: rgb(185 28 28) !important;
        }

        form[onsubmit*="Delete"] button[type="submit"]:not([title="Delete"]),
        .contact-delete-button {
            border-color: rgb(220 38 38) !important;
            background: rgb(220 38 38) !important;
            color: #fff !important;
        }

        form[onsubmit*="Delete"] button[type="submit"]:not([title="Delete"]):hover,
        .contact-delete-button:hover {
            border-color: rgb(185 28 28) !important;
            background: rgb(185 28 28) !important;
            color: #fff !important;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen">
        <div id="mobile-menu-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/30 lg:hidden" onclick="toggleMobileMenu()"></div>

        <aside
            class="fixed left-0 top-0 z-50 flex h-full w-72 -translate-x-full flex-col border-r border-slate-200/50 bg-white/95 shadow-[30px_0_60px_-15px_rgba(0,0,0,0.03)] backdrop-blur-xl transition-transform duration-200 lg:w-64 lg:translate-x-0 lg:bg-white/80"
            id="side-nav">
            <header class="sticky top-0 z-50 flex h-14 items-center justify-between border-b border-slate-200/50 bg-white/90 px-4 backdrop-blur-xl lg:hidden">
                <a href="{{ route('admin.dashboard') }}" class="fl-brand relative flex flex-shrink-0 items-center overflow-hidden" style="height: 40px; width: 90px;" aria-label="BrandIxZen Admin">
                    <img src="{{ asset('picture/Logo.png') }}" alt="BrandIxZen Admin" style="height: 100%; width: 100%; object-fit: contain; object-position: left center;">
                </a>
                <button type="button" aria-label="Close menu"
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100"
                    onclick="toggleMobileMenu()">
                    <span class="material-symbols-outlined !text-[24px]" style="font-variation-settings: 'wght' 300">close</span>
                </button>
            </header>

            <div class="flex min-h-0 flex-1 flex-col px-6 pt-2 lg:pt-4">
                <div class="mb-4 hidden flex-shrink-0 lg:block">
                    <a href="{{ route('admin.dashboard') }}" class="fl-brand relative flex items-center overflow-hidden" style="height: 90px; width: 120px;">
                        <img src="{{ asset('picture/Logo.png') }}" alt="BrandIxZen Admin" style="height: 100%; width: 100%; object-fit: contain; object-position: left center;">
                    </a>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden pb-2 pr-0.5" id="main-nav">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex w-full items-center gap-3 rounded-lg px-3 py-1.5 font-['Space_Grotesk'] text-sm font-semibold tracking-tight transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#5D5CFF]/10 text-[#5D5CFF]' : 'text-slate-600 hover:bg-[#5D5CFF]/5 hover:text-[#5D5CFF]' }}">
                        <span class="material-symbols-outlined !text-[20px]">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                    
                   <div class="pr-0.5">
                        <button type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-1.5 font-['Space_Grotesk'] text-sm font-semibold tracking-tight transition-all duration-200 {{ request()->routeIs('admin.career.*') ? 'bg-[#5D5CFF]/10 text-[#5D5CFF]' : 'text-slate-600 hover:bg-[#5D5CFF]/5 hover:text-[#5D5CFF]' }}"
                            onclick="toggleStaticSubmenu('sub-career')">
                            <span class="material-symbols-outlined !text-[20px]">work</span>
                            <span class="flex-1 text-left">Career</span>
                            <span class="material-symbols-outlined !text-[18px] transition-transform duration-200 {{ request()->routeIs('admin.career.*') ? 'rotate-180' : '' }}" id="chevron-sub-career">expand_more</span>
                        </button>
                        <div class="{{ request()->routeIs('admin.career.*') ? 'submenu-open' : 'submenu-hidden' }} mt-0.5 space-y-0 pl-8" id="sub-career">
                            <a href="{{ route('admin.career.jobs') }}"
                               class="flex w-full items-center gap-2 whitespace-nowrap px-2 py-1 text-left text-xs transition-colors {{ request()->routeIs('admin.career.jobs') ? 'text-[#5D5CFF]' : 'text-slate-500 hover:text-[#5D5CFF]' }}">
                                <span class="material-symbols-outlined !text-[15px]">info</span>
                                <span>Job</span>
                            </a>
                            <a href="{{ route('admin.career.applications') }}"
                               class="flex w-full items-center gap-2 whitespace-nowrap px-2 py-1 text-left text-xs transition-colors {{ request()->routeIs('admin.career.applications') ? 'text-[#5D5CFF]' : 'text-slate-500 hover:text-[#5D5CFF]' }}">
                                <span class="material-symbols-outlined !text-[15px]">handshake</span>
                                <span>Application</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.newsletter.index') }}"
                       class="flex w-full items-center gap-3 rounded-lg px-3 py-1.5 font-['Space_Grotesk'] text-sm font-semibold tracking-tight transition-all duration-200 {{ request()->routeIs('admin.newsletter.*') ? 'bg-[#5D5CFF]/10 text-[#5D5CFF]' : 'text-slate-600 hover:bg-[#5D5CFF]/5 hover:text-[#5D5CFF]' }}">
                        <span class="material-symbols-outlined !text-[20px]">mail</span>
                        <span>Newsletter</span>
                    </a>
                </nav>
            </div>

            <div class="mt-3 flex-shrink-0 border-t border-slate-200/50 px-6 pb-2">
                <div class="relative mt-3">
                    <button type="button"
                            class="flex w-full items-center justify-between gap-3 rounded-xl border border-transparent px-3 py-2.5 text-left transition-all duration-200  hover:bg-slate-50"
                            onclick="toggleAccountMenu()"
                            aria-expanded="false"
                            aria-controls="account-menu"
                            id="account-menu-button">
                        <span class="min-w-0">
                            <span class="block truncate font-['Space_Grotesk'] text-sm font-semibold text-slate-800">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="block text-xs text-slate-400">Admin account</span>
                        </span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200" id="account-menu-chevron">expand_more</span>
                    </button>

                    <div id="account-menu"
                         class="submenu-hidden absolute left-0 right-0 z-50 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-[0_18px_45px_-20px_rgba(15,23,42,0.45)]"
                         style="bottom: calc(100% + 10px);">
                        <a href="{{ route('admin.change-password') }}"
                           class="mx-1.5 flex items-center gap-3 rounded-lg px-3 py-2.5 font-['Space_Grotesk'] text-sm font-semibold text-slate-600 transition hover:bg-[#5D5CFF]/5 hover:text-[#5D5CFF]">
                            <span class="material-symbols-outlined !text-[20px]">lock_reset</span>
                            <span>Change Password</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="mx-1.5 flex w-[calc(100%-12px)] items-center gap-3 rounded-lg px-3 py-2.5 text-left font-['Space_Grotesk'] text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                <span class="material-symbols-outlined !text-[20px]">logout</span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-64">
            <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/95 backdrop-blur lg:hidden">
                <div class="flex h-14 items-center justify-between gap-4 px-4">
                    <a href="{{ route('admin.dashboard') }}" class="fl-brand relative flex min-w-0 items-center overflow-hidden" style="height: 40px; width: 90px;">
                        <img src="{{ asset('picture/Logo.png') }}" alt="BrandIxZen Admin" style="height: 100%; width: 100%; object-fit: contain; object-position: left center;">
                    </a>

                    <button type="button" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition hover:bg-slate-50" onclick="toggleMobileMenu()" aria-label="Open menu">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </header>
            <!-- <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <button type="button" class="flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-700 lg:hidden" onclick="toggleMobileMenu()" aria-label="Open menu">
                        <span class="material-symbols-outlined">menu</span>
                    </button>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-500">Signed in as</p>
                        <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                            Logout
                        </button>
                    </form>
                </div>
            </header> -->

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sideNav = document.getElementById('side-nav');
            const overlay = document.getElementById('mobile-menu-overlay');

            sideNav.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleStaticSubmenu(id) {
            const submenu = document.getElementById(id);
            const chevron = document.getElementById(`chevron-${id}`);

            submenu.classList.toggle('submenu-hidden');
            submenu.classList.toggle('submenu-open');
            chevron.classList.toggle('rotate-180');
        }

        function toggleCtaAccordion(id) {
            const accordionIds = ['sub-cta-ai-insight', 'sub-cta-ai-training', 'sub-cta-ai-solution'];
            const target = document.getElementById(id);
            const shouldOpen = target.classList.contains('submenu-hidden');

            accordionIds.forEach((accordionId) => {
                const submenu = document.getElementById(accordionId);
                const chevron = document.getElementById(`chevron-${accordionId}`);
                const isTarget = accordionId === id;

                submenu.classList.toggle('submenu-hidden', !isTarget || !shouldOpen);
                submenu.classList.toggle('submenu-open', isTarget && shouldOpen);
                chevron.classList.toggle('rotate-180', isTarget && shouldOpen);
            });
        }

        function toggleAccountMenu() {
            const menu = document.getElementById('account-menu');
            const button = document.getElementById('account-menu-button');
            const chevron = document.getElementById('account-menu-chevron');
            const isOpen = menu.classList.toggle('submenu-hidden') === false;

            menu.classList.toggle('submenu-open', isOpen);
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            button.classList.toggle('bg-slate-50', isOpen);
            button.classList.toggle('shadow-sm', isOpen);
            chevron.classList.toggle('rotate-180', isOpen);
        }

        document.addEventListener('click', function (event) {
            const menu = document.getElementById('account-menu');
            const button = document.getElementById('account-menu-button');
            const chevron = document.getElementById('account-menu-chevron');

            if (!menu || !button || button.contains(event.target) || menu.contains(event.target)) {
                return;
            }

            menu.classList.add('submenu-hidden');
            menu.classList.remove('submenu-open');
            button.setAttribute('aria-expanded', 'false');
            button.classList.remove('bg-slate-50', 'shadow-sm');
            chevron.classList.remove('rotate-180');
        });
    </script>
</body>
</html>
