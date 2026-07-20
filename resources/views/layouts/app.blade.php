<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

    @stack('styles')
</head>

@stack('styles')

<body>
    <div class="min-h-screen bg-gray-100 flex flex-col">
        @include('layouts.navigation')
        <x-sidebar />

        <main id="mainContent" class="flex-grow ml-72 transition-[margin-left] duration-300">
            {{ $slot }}
        </main>
        <footer id="footerContent" class="ml-72 transition-[margin-left] duration-300">
            <div class="py-8 px-6 lg:px-8 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} DigiSoft Developers Pvt. Ltd.
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="{{ asset('dflip/js/dflip.min.js') }}" defer></script>
    <script src="{{ asset('js/index.js') }}"></script>

    @stack('scripts')

</body>

</html>
