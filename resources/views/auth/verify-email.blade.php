<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KasPro') }} — Verifikasi Email</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased overflow-x-hidden selection:bg-indigo-500/30">
        <div class="relative min-h-screen lg:grid lg:grid-cols-2">

            <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-12 sm:px-8">
                <div class="mesh-gradient absolute inset-0 opacity-40 lg:hidden"></div>

                <div class="relative w-full max-w-md">
                    <div class="animate-glow-pulse absolute -inset-6 -z-10 rounded-full bg-indigo-600/25 blur-3xl"></div>

                    <div class="animate-rise mb-8 flex flex-col items-center text-center">
                        <img src="{{ asset('logo.png') }}" alt="Logo {{ config('app.name', 'KasPro') }}"
                             class="animate-float-logo h-16 w-16 rounded-2xl bg-white/10 p-2 object-contain shadow-lg shadow-indigo-950 ring-1 ring-white/20" />
                        <h2 class="mt-4 text-3xl font-bold tracking-tight">Verifikasi Email Anda</h2>
                    </div>

                    <div class="animate-rise rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-indigo-950/50 backdrop-blur-xl" style="animation-delay: 0.12s">
                        <p class="text-sm text-slate-300">
                            Terima kasih telah mendaftar! Sebelum memulai, verifikasi alamat email Anda dengan mengklik tautan yang kami kirimkan ke email Anda.
                            Jika tidak menerima email, kami akan dengan senang mengirimkannya lagi.
                        </p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mt-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300">
                                Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
                            </div>
                        @endif

                        <div class="mt-6 flex items-center justify-between">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg bg-white/5 border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-200 transition-all hover:border-white/20 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/60">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Kirim Ulang Email Verifikasi
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="text-sm text-slate-400 transition hover:text-slate-200 hover:underline">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            <aside class="relative hidden overflow-hidden lg:block">
                <div class="mesh-gradient absolute inset-0"></div>
                <div class="grid-overlay absolute inset-0"></div>

                <div class="mesh-blob absolute -left-20 -top-20 h-[26rem] w-[26rem] rounded-full bg-indigo-600/40 blur-3xl" style="--float-dur:20s"></div>
                <div class="mesh-blob absolute -right-24 bottom-[8%] h-[24rem] w-[24rem] rounded-full bg-purple-600/40 blur-3xl" style="--float-dur:28s"></div>

                <div class="relative z-10 flex h-full flex-col justify-center px-14 py-12">
                    <h1 class="bg-gradient-to-r from-indigo-300 via-purple-300 to-cyan-300 bg-clip-text text-4xl font-bold leading-tight text-transparent xl:text-5xl">
                        Satu langkah lagi.
                    </h1>
                    <p class="mt-5 max-w-md text-lg text-slate-400">
                        Verifikasi email Anda untuk mulai mengakses {{ config('app.name', 'KasPro') }}.
                    </p>
                </div>
            </aside>
        </div>
    </body>
</html>