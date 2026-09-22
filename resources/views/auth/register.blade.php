<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KasPro') }} — Daftar Akun Baru</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased overflow-x-hidden selection:bg-indigo-500/30">
        <div class="relative min-h-screen lg:grid lg:grid-cols-2">

            <!-- ======= Form side ======= -->
            <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-12 sm:px-8">
                <div class="mesh-gradient absolute inset-0 opacity-40 lg:hidden"></div>
                <div class="mesh-blob absolute -right-24 top-1/4 h-72 w-72 rounded-full bg-purple-600/30 blur-3xl lg:hidden" style="--float-dur:20s"></div>

                <div class="relative w-full max-w-md">
                    <div class="animate-glow-pulse absolute -inset-6 -z-10 rounded-full bg-indigo-600/25 blur-3xl"></div>

                    <!-- Brand -->
                    <div class="animate-rise mb-8 flex flex-col items-center text-center">
                        <img src="{{ asset('logo.png') }}" alt="Logo {{ config('app.name', 'KasPro') }}"
                             class="animate-float-logo h-16 w-16 rounded-2xl bg-white/10 p-2 object-contain shadow-lg shadow-indigo-950 ring-1 ring-white/20" />
                        <h2 class="mt-4 text-3xl font-bold tracking-tight">Buat Akun Baru</h2>
                        <p class="mt-1.5 text-sm text-slate-400">Daftar untuk mulai mengelola keuangan Anda.</p>
                    </div>

                    <!-- Session status -->
                    @if (session('status'))
                        <div class="animate-fade-in mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Kartu glassmorphism -->
                    <div class="animate-rise rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-indigo-950/50 backdrop-blur-xl" style="animation-delay: 0.12s">
                        <form method="POST" action="{{ route('register') }}" x-data="{ loading: false }" x-on:submit="loading = true">
                            @csrf

                            <!-- Name -->
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-300">Nama Lengkap</label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                       placeholder="Nama Anda"
                                       class="w-full rounded-lg border bg-white/5 px-4 py-2.5 text-sm text-white transition-all duration-300 placeholder:text-slate-500 hover:border-white/20 focus:bg-white/10 focus:outline-none
                                              {{ $errors->has('name')
                                                  ? 'animate-shake border-rose-500/70 ring-2 ring-rose-500/25'
                                                  : 'border-white/10 focus:border-indigo-400/70 focus:ring-2 focus:ring-indigo-500/30' }}" />
                                @error('name')
                                    <p class="animate-fade-in mt-2 flex items-center gap-1.5 text-sm text-rose-400">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.7 4h13.4a1.87 1.87 0 001.28-3.2l-6.7-11.2a1.87 1.87 0 00-3.28 0l-6.7 11.2A1.87 1.87 0 003.94 19h13.4z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mt-5">
                                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                       placeholder="nama@perusahaan.com"
                                       class="w-full rounded-lg border bg-white/5 px-4 py-2.5 text-sm text-white transition-all duration-300 placeholder:text-slate-500 hover:border-white/20 focus:bg-white/10 focus:outline-none
                                              {{ $errors->has('email')
                                                  ? 'animate-shake border-rose-500/70 ring-2 ring-rose-500/25'
                                                  : 'border-white/10 focus:border-indigo-400/70 focus:ring-2 focus:ring-indigo-500/30' }}" />
                                @error('email')
                                    <p class="animate-fade-in mt-2 flex items-center gap-1.5 text-sm text-rose-400">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.7 4h13.4a1.87 1.87 0 001.28-3.2l-6.7-11.2a1.87 1.87 0 00-3.28 0l-6.7 11.2A1.87 1.87 0 003.94 19h13.4z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mt-5">
                                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Kata Sandi</label>
                                <div x-data="{ show: false }" class="relative">
                                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                           placeholder="••••••••"
                                           class="w-full rounded-lg border bg-white/5 px-4 py-2.5 pr-11 text-sm text-white transition-all duration-300 placeholder:text-slate-500 hover:border-white/20 focus:bg-white/10 focus:outline-none
                                                  {{ $errors->has('password')
                                                      ? 'animate-shake border-rose-500/70 ring-2 ring-rose-500/25'
                                                      : 'border-white/10 focus:border-indigo-400/70 focus:ring-2 focus:ring-indigo-500/30' }}" />
                                    <button type="button" @click="show = ! show" aria-label="Tampilkan kata sandi"
                                            class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 transition hover:text-slate-200">
                                        <svg x-show="! show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" class="h-5 w-5" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="animate-fade-in mt-2 flex items-center gap-1.5 text-sm text-rose-400">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.7 4h13.4a1.87 1.87 0 001.28-3.2l-6.7-11.2a1.87 1.87 0 00-3.28 0l-6.7 11.2A1.87 1.87 0 003.94 19h13.4z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="mt-5">
                                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-300">Konfirmasi Kata Sandi</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                       placeholder="••••••••"
                                       class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white transition-all duration-300 placeholder:text-slate-500 hover:border-white/20 focus:border-indigo-400/70 focus:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                            </div>

                            <!-- Submit -->
                            <button type="submit" :disabled="loading"
                                    class="mt-7 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl hover:shadow-indigo-400/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/60 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70 disabled:hover:scale-100">
                                <svg x-show="! loading" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <svg x-show="loading" style="display:none" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span x-text="loading ? 'Memproses...' : 'Daftar'">Daftar</span>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-white/10"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white/5 px-3 text-xs font-medium text-slate-500">ATAU</span>
                            </div>
                        </div>

                        <!-- Google -->
                        <a href="{{ route('google.redirect') }}"
                           class="flex w-full items-center justify-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition-all duration-300 hover:border-white hover:bg-slate-50 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/60">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                            </svg>
                            Daftar dengan Google
                        </a>
                    </div>

                    <p class="animate-rise mt-6 text-center text-xs text-slate-500" style="animation-delay: 0.24s">
                        Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 hover:underline">Masuk sekarang</a>
                    </p>
                </div>
            </main>

            <!-- ======= Visual panel (desktop) ======= -->
            <aside class="relative hidden overflow-hidden lg:block">
                <div class="mesh-gradient absolute inset-0"></div>
                <div class="grid-overlay absolute inset-0"></div>

                <div class="mesh-blob absolute -left-20 -top-20 h-[26rem] w-[26rem] rounded-full bg-indigo-600/40 blur-3xl" style="--float-dur:20s"></div>
                <div class="mesh-blob absolute -right-24 bottom-[8%] h-[24rem] w-[24rem] rounded-full bg-purple-600/40 blur-3xl" style="--float-dur:28s"></div>
                <div class="mesh-blob absolute left-[52%] top-[45%] h-72 w-72 rounded-full bg-cyan-500/25 blur-3xl" style="--float-dur:34s"></div>

                <div class="relative z-10 flex h-full flex-col justify-between px-14 py-12">
                    <div class="animate-rise flex items-center gap-3">
                        <img src="{{ asset('logo.png') }}" alt="" class="h-10 w-10 rounded-lg bg-white/10 p-1.5 object-contain ring-1 ring-white/20" />
                        <span class="text-2xl font-bold tracking-tight">{{ config('app.name', 'KasPro') }}</span>
                    </div>

                    <div class="animate-rise" style="animation-delay: 0.15s">
                        <h1 class="bg-gradient-to-r from-indigo-300 via-purple-300 to-cyan-300 bg-clip-text text-4xl font-bold leading-tight text-transparent xl:text-5xl">
                            Mulai kelola keuangan<br />dari sini.
                        </h1>
                        <p class="mt-5 max-w-md text-lg text-slate-400">
                            Buat akun gratis untuk mulai mencatat transaksi, melacak piutang & hutang, hingga membuat laporan keuangan otomatis.
                        </p>
                    </div>

                    <div class="animate-rise space-y-3" style="animation-delay: 0.3s">
                        @foreach ([
                            ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'Data aman & terisolasi per akun'],
                            ['M13 10V3L4 14h7v7l9-11h-7z', 'Pencatatan instan & jurnal otomatis'],
                            ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'Dashboard & laporan real-time'],
                        ] as [$icon, $text])
                            <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm">
                                <svg class="h-5 w-5 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                                <span class="text-sm text-slate-300">{{ $text }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </body>
</html>