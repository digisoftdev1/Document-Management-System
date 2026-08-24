@php
    $documentActive = request()->routeIs('documents.*') || request()->routeIs('repository.*');

    $settingsActive =
        request()->routeIs('fiscal-years.*') ||
        request()->routeIs('government-levels.*') ||
        request()->routeIs('budget-types.*') ||
        request()->routeIs('settings.*') ||
        request()->routeIs('users.*');
@endphp

<aside id="sidebar"
    class="fixed left-0 top-0 z-40 h-screen bg-[#03396c] transition-all duration-300 {{ request()->cookie('miniSidebar') === 'true' ? 'w-20' : 'w-72' }}">
    <script>
        if (localStorage.getItem('miniSidebar') === 'true') {
            document.getElementById('sidebar').classList.add('w-20');
            document.getElementById('sidebar').classList.remove('w-72');
        } else {
            document.getElementById('sidebar').classList.add('w-72');
            document.getElementById('sidebar').classList.remove('w-20');
        }
    </script>
    <div class="h-full py-[5.5rem] overflow-y-auto">

        {{-- Toggle --}}
        <div class="flex justify-end px-3 sidebar-item">
            <button id="sidebarToggle"
                class="flex justify-center items-center size-8 text-white hover:bg-blue-900 transition-colors rounded-lg">
                <svg id="sidebarArrow" class="size-4 transition duration-300" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M15 3v18" />
                    <path d="m10 15-3-3 3-3" />
                </svg>
            </button>
        </div>

        <ul class="space-y-1 font-medium w-full px-0">

            <li>
                <a href="{{ route('dashboard') }}" @class([
                    'sidebar-item flex items-center w-full px-4 py-2.5 text-[15px] transition duration-200',
                    'bg-blue-900 text-white' => request()->routeIs('dashboard'),
                    'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                        'dashboard'),
                ])>
                    <i class="fa-solid fa-gauge w-5 text-center"></i>
                    <span class="sidebar-label flex-1 ml-3 whitespace-nowrap">
                        {{ __('Dashboard') }}
                    </span>
                </a>
            </li>

            <li>
                <button id="documentManagementBtn" type="button" @class([
                    'sidebar-item flex items-center w-full px-4 py-2.5 text-[15px] transition duration-200',
                    'bg-blue-900 text-white' => $documentActive,
                    'text-blue-100 hover:bg-blue-900 hover:text-white' => !$documentActive,
                ])>
                    <i class="fa-solid fa-folder-tree w-5 text-center parent-icon"></i>

                    <span class="sidebar-label flex-1 ml-3 text-left whitespace-nowrap">
                        {{ __('Document Management') }}
                    </span>

                    <i id="documentChevron"
                        class="sidebar-label fa-solid fa-chevron-down text-xs transition duration-300 rotate-180">
                    </i>
                </button>

                <ul id="documentManagementMenu" class="py-1 space-y-1">
                    <li>
                        <a href="{{ route('documents.index') }}" @class([
                            'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                            'bg-blue-800 text-white' => request()->routeIs('documents.*'),
                            'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                'documents.*'),
                        ])
                            title="{{ __('Records') }}">
                            <i class="fa-solid fa-file-lines w-5 text-center"></i>
                            <span class="sidebar-label ml-3 whitespace-nowrap">
                                {{ __('Records') }}
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('repository.dashboard') }}" @class([
                            'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                            'bg-blue-800 text-white' => request()->routeIs('repository.*'),
                            'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                'repository.*'),
                        ])
                            title="{{ __('Repository') }}">
                            <i class="fa-solid fa-folder w-5 text-center"></i>
                            <span class="sidebar-label ml-3 whitespace-nowrap">
                                {{ __('Repository') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <button id="settingsBtn" @class([
                    'sidebar-item flex items-center w-full px-4 py-2.5 text-[15px] transition duration-200',
                    'bg-blue-900 text-white' => $settingsActive,
                    'text-blue-100 hover:bg-blue-900 hover:text-white' => !$settingsActive,
                ])>
                    <i class="fa-solid fa-gear w-5 text-center parent-icon"></i>

                    <span class="sidebar-label flex-1 ml-3 text-left whitespace-nowrap">
                        {{ __('Setup') }}
                    </span>

                    <i id="settingsChevron"
                        class="sidebar-label fa-solid fa-chevron-down text-xs transition duration-300">
                    </i>
                </button>

                <ul id="settingsMenu" class="py-1 space-y-1 {{ $settingsActive ? '' : 'hidden' }}">
                    <li>
                        <a href="{{ route('fiscal-years.index') }}" @class([
                            'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                            'bg-blue-800 text-white' => request()->routeIs('fiscal-years.*'),
                            'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                'fiscal-years.*'),
                        ])
                            title="{{ __('Fiscal Years') }}">
                            <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                            <span class="sidebar-label ml-3 whitespace-nowrap">
                                {{ __('Fiscal Years') }}
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('government-levels.index') }}" @class([
                            'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                            'bg-blue-800 text-white' => request()->routeIs('government-levels.*'),
                            'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                'government-levels.*'),
                        ])
                            title="{{ __('Government Bodies') }}">
                            <i class="fa-solid fa-building-columns w-5 text-center"></i>
                            <span class="sidebar-label ml-3 whitespace-nowrap">
                                {{ __('Government Bodies') }}
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('budget-types.index') }}" @class([
                            'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                            'bg-blue-800 text-white' => request()->routeIs('budget-types.*'),
                            'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                'budget-types.*'),
                        ])
                            title="{{ __('Program Types') }}">
                            <i class="fa-solid fa-diagram-project w-5 text-center"></i>
                            <span class="sidebar-label ml-3 whitespace-nowrap">
                                {{ __('Program Types') }}
                            </span>
                        </a>
                    </li>

                    @if (auth()->user()?->role?->slug === 'Superadmin' || auth()->user()?->role?->slug === 'Admin')
                        <li>
                            <a href="{{ route('users.index') }}" @class([
                                'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                                'bg-blue-800 text-white' => request()->routeIs('users.*'),
                                'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                    'users.*'),
                            ])
                                title="{{ __('Users') }}">
                                <i class="fa-solid fa-user w-5 text-center"></i>
                                <span class="sidebar-label ml-3 whitespace-nowrap">
                                    {{ __('Users') }}
                                </span>
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('settings.website') }}" @class([
                            'submenu-item flex items-center w-full pl-12 pr-4 py-2 text-[15px] transition duration-200',
                            'bg-blue-800 text-white' => request()->routeIs('settings.*'),
                            'text-blue-100 hover:bg-blue-900 hover:text-white' => !request()->routeIs(
                                'settings.*'),
                        ])
                            title="{{ __('System Info') }}">
                            <i class="fa-solid fa-globe w-5 text-center"></i>
                            <span class="sidebar-label ml-3 whitespace-nowrap">
                                {{ __('System Info') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>

    </div>

</aside>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('mainContent');
            const footer = document.getElementById('footerContent');
            const toggle = document.getElementById('sidebarToggle');
            const arrow = document.getElementById('sidebarArrow');

            const docBtn = document.getElementById('documentManagementBtn');
            const docMenu = document.getElementById('documentManagementMenu');
            const docChevron = document.getElementById('documentChevron');

            const settingsBtn = document.getElementById('settingsBtn');
            const settingsMenu = document.getElementById('settingsMenu');
            const settingsChevron = document.getElementById('settingsChevron');

            let documentMenuOpen = true;
            let settingsMenuOpen = @json($settingsActive ?? false);
            let miniSidebar = localStorage.getItem('miniSidebar') === 'true';

            function applySidebar() {
                document.documentElement.classList.toggle('mini-sidebar-active', miniSidebar);
                sidebar?.classList.toggle('w-20', miniSidebar);
                sidebar?.classList.toggle('w-72', !miniSidebar);

                main?.classList.toggle('ml-20', miniSidebar);
                main?.classList.toggle('ml-72', !miniSidebar);

                footer?.classList.toggle('ml-20', miniSidebar);
                footer?.classList.toggle('ml-72', !miniSidebar);

                arrow?.classList.toggle('rotate-180', miniSidebar);

                document.querySelectorAll('.sidebar-label').forEach(el => {
                    el.classList.toggle('hidden', miniSidebar);
                });

                document.querySelectorAll('.parent-icon').forEach(el => {
                    el.classList.toggle('hidden', miniSidebar);
                });

                docBtn?.classList.toggle('hidden', miniSidebar);
                settingsBtn?.classList.toggle('hidden', miniSidebar);

                document.querySelectorAll('.sidebar-item').forEach(el => {
                    el.classList.toggle('justify-center', miniSidebar);
                });

                document.querySelectorAll('.submenu-item').forEach(el => {
                    el.classList.toggle('justify-center', miniSidebar);
                    el.classList.toggle('pl-12', !miniSidebar);
                    el.classList.toggle('px-4', miniSidebar);
                });

                // Handle menus visibility
                if (miniSidebar) {
                    docMenu?.classList.remove('hidden');
                    settingsMenu?.classList.remove('hidden');
                } else {
                    docMenu?.classList.toggle('hidden', !documentMenuOpen);
                    settingsMenu?.classList.toggle('hidden', !settingsMenuOpen);
                }
            }

            applySidebar();

            toggle?.addEventListener('click', (e) => {
                e.preventDefault();
                miniSidebar = !miniSidebar;
                localStorage.setItem('miniSidebar', miniSidebar);
                applySidebar();
            });

            docBtn?.addEventListener('click', () => {
                if (miniSidebar) return;
                documentMenuOpen = !documentMenuOpen;
                docMenu?.classList.toggle('hidden', !documentMenuOpen);
                docChevron?.classList.toggle('rotate-180', documentMenuOpen);
            });

            settingsBtn?.addEventListener('click', () => {
                if (miniSidebar) return;
                settingsMenuOpen = !settingsMenuOpen;
                settingsMenu?.classList.toggle('hidden', !settingsMenuOpen);
                settingsChevron?.classList.toggle('rotate-180', settingsMenuOpen);
            });
        });
    </script>
@endpush
