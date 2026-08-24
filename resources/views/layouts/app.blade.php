<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ request()->cookie('miniSidebar') === 'true' ? 'mini-sidebar-active' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $setting?->website_title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="icon"
        href="{{ $setting?->favicon ? Storage::url($setting->favicon) : asset('assets/favicon/favicon-32x32.png') }}">

    <link href="{{ asset('dflip/css/dflip.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dflip/css/themify-icons.min.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('miniSidebar') === 'true') {
            document.documentElement.classList.add('mini-sidebar-active');
        } else {
            document.documentElement.classList.remove('mini-sidebar-active');
        }
    </script>

    <style>
        #sidebar {
            width: 18rem;
        }

        #mainContent,
        #footerContent {
            margin-left: 18rem;
        }

        .mini-sidebar-active #sidebar {
            width: 5rem;
        }

        .mini-sidebar-active #mainContent,
        .mini-sidebar-active #footerContent {
            margin-left: 5rem;
        }

        .mini-sidebar-active .sidebar-label,
        .mini-sidebar-active .parent-icon,
        .mini-sidebar-active #documentManagementBtn,
        .mini-sidebar-active #settingsBtn {
            display: none !important;
        }

        .mini-sidebar-active .sidebar-item {
            justify-content: center !important;
        }

        .mini-sidebar-active .submenu-item {
            justify-content: center !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="min-h-screen bg-gray-100 flex flex-col">
        @include('layouts.navigation')
        <x-sidebar />

        <main id="mainContent" class="flex-grow transition-[margin-left] duration-300">
            {{ $slot }}
        </main>

        <footer id="footerContent" class="transition-[margin-left] duration-300">
            <div class="py-8 px-6 lg:px-8 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} DigiSoft Developers Pvt. Ltd.
            </div>
        </footer>
    </div>

    <script src="{{ asset('dflip/js/dflip.min.js') }}" defer></script>
    <script src="{{ asset('js/index.js') }}"></script>

    @stack('scripts')
</body>

</html>
