<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'KasPro') }} @isset($title) — {{ $title }} @endisset</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navbar')

            <div class="flex-1 flex flex-col min-h-screen">
                <main class="flex-1 pb-20 lg:pb-8 px-3 sm:px-6 lg:px-8 py-6">
                    @if(session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif
                    @if(session('warning'))
                        <x-alert type="warning" :message="session('warning')" />
                    @endif
                    @if(session('error'))
                        <x-alert type="error" :message="session('error')" />
                    @endif
                    @if(session('info'))
                        <x-alert type="info" :message="session('info')" />
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @include('layouts.bottom-nav')
        @include('layouts.mobile-drawer')
        @include('layouts.chat-fab')

        <div id="menu-overlay" class="hidden fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
             onclick="const d=document.getElementById('mobile-drawer'); if(d) d.classList.add('-translate-x-full'); this.classList.add('hidden');">
        </div>

        @stack('scripts')
    </body>
</html>
