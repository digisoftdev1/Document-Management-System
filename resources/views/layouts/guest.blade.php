<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Document Management System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/site.webmanifest') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body
    class="font-sans antialiased min-h-screen bg-gradient-to-br from-slate-100 via-blue-50 to-slate-200 overflow-hidden">

    <div class="absolute top-[-120px] left-[-120px] h-96 w-96 rounded-full bg-blue-300/30 blur-3xl">
    </div>

    <div class="absolute bottom-[-150px] right-[-150px] h-[500px] w-[500px] rounded-full bg-indigo-300/20 blur-3xl">
    </div>

    <div class="relative min-h-screen flex items-center justify-center px-6">

        {{ $slot }}

    </div>

</body>

</html>
