@unless(request()->routeIs('chat.index'))
    @if(Auth::check())
    <div x-data="chatFab()"
         class="fixed z-40 right-4 bottom-20 lg:bottom-6 flex flex-col items-end gap-3"
         aria-live="polite">

        {{-- Popup percakapan komunitas --}}
        <div x-show="buka" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100"
             x-cloak
             class="w-[calc(100vw-2.5rem)] max-w-sm bg-white rounded-2xl border border-gray-200 shadow-2xl overflow-hidden flex flex-col"
             :class="buka ? 'h-[28rem]' : ''">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="truncate text-sm font-bold text-gray-800">Chat Komunitas</p>
                    <p class="text-xs text-gray-400">Semua pengguna KasPro</p>
                </div>
                <a href="{{ str_replace(url('/'), '', route('chat.index')) }}" aria-label="Buka chat penuh"
                   class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </a>
                <button type="button" @click="buka = false" aria-label="Tutup chat"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="relative flex-1 overflow-hidden">
                <div x-ref="kotakPesan" @scroll="cekScroll()"
                     class="h-full overflow-y-auto px-4 py-3 space-y-3 bg-gray-50/60">
                    <template x-if="memuat">
                        <p class="text-center text-sm text-gray-400 py-6">Memuat pesan…</p>
                    </template>

                    <template x-for="m in pesanTampil" :key="m.id">
                        <div>
                            <div x-show="m.pembagi"
                                 class="flex justify-center py-1">
                                <span class="text-[11px] text-gray-500 bg-gray-200/70 px-3 py-1 rounded-full" x-text="m.label"></span>
                            </div>

                            <div x-show="!m.pembagi && m.moderated"
                                 class="flex justify-center py-1">
                                <span class="text-xs text-gray-400 italic bg-gray-100 px-3 py-1 rounded-full">Pesan dihapus oleh moderator</span>
                            </div>

                            <div x-show="!m.pembagi && !m.moderated"
                                 :class="m.own ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="m.own ? 'flex-row-reverse' : 'flex-row'"
                                     class="flex items-end gap-2 max-w-[92%]">
                                    <span :class="m.sender_color"
                                          class="w-8 h-8 rounded-full text-white text-sm font-bold flex items-center justify-center shrink-0 shadow-sm"
                                          x-text="m.sender_initial"></span>
                                    <div class="min-w-0 flex flex-col" :class="m.own ? 'items-end' : 'items-start'">
                                        <p :class="m.own ? 'text-emerald-700' : 'text-gray-500'"
                                           class="text-[10px] font-semibold mb-0.5 px-1 leading-none">
                                            <span x-text="m.sender_name"></span>
                                        </p>
                                        <div :class="m.own ? 'bg-emerald-600 text-white rounded-br-sm' : 'bg-white border border-gray-200 rounded-bl-sm'"
                                             class="px-3 py-2 rounded-2xl shadow-sm">
                                            <p class="text-sm whitespace-pre-wrap break-words" x-text="m.body"></p>
                                            <p :class="m.own ? 'text-emerald-100' : 'text-gray-400'" class="text-[10px] mt-1 text-right leading-none">
                                                <span x-text="m.waktu"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button x-show="pesanBaruCount > 0" @click="bukaPesanBaru()"
                        class="absolute bottom-3 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-600 text-white text-xs font-semibold shadow-lg hover:bg-emerald-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    <span x-text="pesanBaruCount + ' pesan baru'"></span>
                </button>
            </div>

            <div class="shrink-0 bg-white">
                <div class="flex items-center gap-0.5 px-3 pt-2 text-lg">
                    <template x-for="e in emojis" :key="e">
                        <button type="button" @click="tambahEmoji(e)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                            <span x-text="e"></span>
                        </button>
                    </template>
                </div>
                <form @submit.prevent="kirim()" class="p-3 border-t border-gray-100 flex items-end gap-2">
                    <textarea x-ref="inputPesan" x-model="body" rows="1" maxlength="2000"
                              placeholder="Tulis pesan… (Enter untuk kirim)"
                              @keydown.enter.exact.prevent="kirim()"
                              @input="tumbuh($el)"
                              class="flex-1 resize-none max-h-32 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 overflow-y-auto"></textarea>
                    <button type="submit" :disabled="!body.trim()"
                            :class="body.trim() ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-200 cursor-not-allowed'"
                            class="inline-flex items-center justify-center h-10 w-10 rounded-lg text-white transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Tombol FAB --}}
        <button type="button" @click="toggle()" :aria-label="buka ? 'Tutup chat' : 'Buka chat'"
                class="relative w-14 h-14 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg hover:bg-emerald-700 transition">
            <svg x-show="!buka" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <svg x-show="buka" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <template x-if="!buka && total > 0">
                <span class="absolute -top-0.5 -right-0.5 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center" x-text="total > 99 ? '99+' : total"></span>
            </template>
        </button>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatFab', () => ({
            buka: false,
            memuat: false,
            total: 0,
            body: '',
            pesan: [],
            pesanTampil: [],
            lastId: 0,
            pesanBaruCount: 0,
            interval: null,
            intervalBadge: null,
            emojis: ['😀', '😂', '❤️', '👍', '😮', '😢', '🔥', '✅'],
            urlPesan: '{{ str_replace(url('/'), '', route('chat.pesan', \App\Models\ChatRoom::ruangKomunitas()->id)) }}',
            urlKirim: '{{ str_replace(url('/'), '', route('chat.kirim', \App\Models\ChatRoom::ruangKomunitas()->id)) }}',

            init() {
                this.sinkronBadge();
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden && this.buka) this.sinkron(true);
                });
                this.interval = setInterval(() => {
                    if (document.hidden) return;
                    if (this.buka) this.sinkron(true);
                }, 2000);
                this.intervalBadge = setInterval(() => this.sinkronBadge(), 5000);
            },

            async toggle() {
                this.buka = !this.buka;
                if (this.buka) {
                    if (this.pesan.length === 0) {
                        await this.muatAwal();
                    } else {
                        await this.sinkron(true);
                        this.scrollBawah(true);
                    }
                }
            },

            async muatAwal() {
                this.memuat = true;
                try {
                    const r = await fetch(this.urlPesan + '?after=0&mark=1');
                    const data = await r.json();
                    this.pesan = data;
                    this.lastId = data.length ? data[data.length - 1].id : 0;
                    this.total = 0;
                    this.pesanBaruCount = 0;
                    this.olahPesan();
                    this.sinkronBadge();
                } catch (e) {
                } finally {
                    this.memuat = false;
                }
                this.scrollBawah(true);
            },

            async sinkron(mark) {
                try {
                    const r = await fetch(this.urlPesan + '?after=' + this.lastId + (mark ? '&mark=1' : ''));
                    const data = await r.json();
                    if (data.length) {
                        const dekatBawah = this.diBawah();
                        const adaMilikSendiri = data.some(m => m.own);
                        this.pesan = this.pesan.concat(data);
                        this.lastId = data[data.length - 1].id;
                        this.olahPesan();
                        if (dekatBawah || adaMilikSendiri) {
                            this.pesanBaruCount = 0;
                            this.scrollBawah(true);
                        } else {
                            this.pesanBaruCount += data.length;
                        }
                        this.total = 0;
                    }
                } catch (e) {
                }
            },

            async sinkronBadge() {
                try {
                    const r = await fetch('{{ str_replace(url('/'), '', route('chat.unread-count')) }}');
                    const j = await r.json();
                    this.total = j.total ?? 0;
                } catch (e) {
                }
            },

            async kirim() {
                const text = (this.body || '').trim();
                if (!text) return;
                this.body = '';
                try {
                    const r = await fetch(this.urlKirim, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ body: text }),
                    });
                    if (r.status === 422) {
                        const j = await r.json();
                        alert(j.errors && j.errors.body ? j.errors.body[0] : 'Pesan gagal dikirim.');
                        return;
                    }
                    const j = await r.json();
                    this.pesan = this.pesan.concat(j);
                    this.lastId = j.id;
                    this.pesanBaruCount = 0;
                    this.olahPesan();
                    this.scrollBawah(true);
                } catch (e) {
                }
            },

            tambahEmoji(e) {
                this.body += e;
                this.$nextTick(() => {
                    this.tumbuh(this.$refs.inputPesan);
                    if (this.$refs.inputPesan) this.$refs.inputPesan.focus();
                });
            },

            tumbuh(el) {
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 128) + 'px';
            },

            cekScroll() {
                const el = this.$refs.kotakPesan;
                if (!el) return;
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 40 && this.pesanBaruCount > 0) {
                    this.pesanBaruCount = 0;
                }
            },

            bukaPesanBaru() {
                this.pesanBaruCount = 0;
                this.scrollBawah(true);
            },

            diBawah() {
                const el = this.$refs.kotakPesan;
                if (!el) return true;
                return el.scrollHeight - el.scrollTop - el.clientHeight < 80;
            },

            olahPesan() {
                const tampil = [];
                let kunciSebelum = null;
                for (const m of this.pesan) {
                    if (m.moderated) {
                        tampil.push({ id: 'mod-' + m.id, pembagi: false, moderated: true, own: m.own });
                        continue;
                    }
                    const kunci = this.kunciHari(m.created_at);
                    if (kunci !== kunciSebelum) {
                        tampil.push({ id: 'hari-' + kunci, pembagi: true, label: this.labelHari(m.created_at) });
                        kunciSebelum = kunci;
                    }
                    tampil.push({
                        id: m.id,
                        own: m.own,
                        moderated: false,
                        body: m.body,
                        sender_name: m.own ? 'Anda' : (m.sender_name || 'Pengguna'),
                        sender_initial: m.sender_initial || '?',
                        sender_color: m.sender_color || 'bg-emerald-600',
                        waktu: this.waktu(m.created_at),
                    });
                }
                this.pesanTampil = tampil;
            },

            kunciHari(iso) {
                if (!iso) return '';
                try {
                    const d = new Date(iso);
                    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                } catch (e) { return ''; }
            },

            labelHari(iso) {
                if (!iso) return '';
                try {
                    const d = new Date(iso);
                    const mulai = (x) => new Date(x.getFullYear(), x.getMonth(), x.getDate()).getTime();
                    const selisih = Math.round((mulai(new Date()) - mulai(d)) / 86400000);
                    if (selisih === 0) return 'Hari Ini';
                    if (selisih === 1) return 'Kemarin';
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                } catch (e) { return ''; }
            },

            waktu(iso) {
                if (!iso) return '';
                try {
                    return new Date(iso).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                } catch (e) {
                    return '';
                }
            },

            scrollBawah(paksa = false) {
                const turun = () => {
                    const el = this.$refs.kotakPesan;
                    if (!el) return;
                    if (paksa || this.diBawah()) el.scrollTop = el.scrollHeight;
                };
                this.$nextTick(() => {
                    turun();
                    requestAnimationFrame(() => requestAnimationFrame(turun));
                });
            },
        }));
    });
    </script>
    @endif
@endunless