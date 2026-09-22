<x-app-layout>
    <x-slot name="title">Chat</x-slot>

    @php
        $roomsJson = collect($ruangan)->map(function ($r) {
            return [
                'id' => (int) $r['room']->id,
                'tipe' => $r['room']->tipe,
                'nama' => $r['nama'] ?? 'Tanpa nama',
                'unread' => (int) $r['unread'],
                'urlPesan' => str_replace(url('/'), '', route('chat.pesan', $r['room']->id)),
                'urlKirim' => str_replace(url('/'), '', route('chat.kirim', $r['room']->id)),
            ];
        })->values()->all();

        $pesanJson = collect($pesanAwal)->values()->all();
    @endphp

    <x-page-header title="Chat" subtitle="Komunitas pengguna KasPro & perpesanan pribadi"></x-page-header>

    <div x-data="chatApp(@js($roomsJson), @js($pesanJson), {{ (int) $roomAktifId }}, {{ (int) $lastIdAwal }})"
        class="flex flex-col lg:flex-row gap-0 lg:gap-5 h-[calc(100vh-14rem)] lg:h-[calc(100vh-12rem)] min-h-[440px]">

        {{-- Daftar ruangan --}}
        <div :class="aktif !== null ? 'hidden lg:flex' : 'flex'"
            class="flex-col w-full lg:w-80 shrink-0 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="p-3 border-b border-gray-100 space-y-2">
                <div class="relative">
                    <input type="text" x-model="q" @input.debounce.300ms="cari()" placeholder="Cari akun untuk chat…"
                        class="w-full h-9 px-3 pr-8 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" />
                    <svg class="w-4 h-4 absolute right-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>

                    <div x-show="hasilCari.length" x-cloak
                        class="absolute z-20 mt-1 w-full bg-white rounded-lg border border-gray-200 shadow-lg overflow-hidden">
                        <template x-for="u in hasilCari" :key="u.id">
                            <button type="button" @click="bukaDm(u)"
                                class="flex items-center gap-2 w-full px-3 py-2 text-left text-sm hover:bg-gray-50">
                                <span class="flex-1 min-w-0">
                                    <span class="block truncate font-medium text-gray-800" x-text="u.name"></span>
                                    <span class="block truncate text-xs text-gray-400" x-text="u.email"></span>
                                </span>
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
                <template x-for="r in rooms" :key="r.id">
                    <button type="button" @click="pilihRuangan(r)"
                        :class="aktif?.id === r.id ? 'bg-emerald-50/70' : 'hover:bg-gray-50'"
                        class="w-full text-left px-4 py-3 flex items-center gap-3 transition">
                        <span class="flex-1 min-w-0">
                            <span class="flex items-center gap-2">
                                <span class="truncate text-sm font-semibold text-gray-800" x-text="r.nama"></span>
                                <span x-show="r.tipe === 'global'"
                                    class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-semibold uppercase">Komunitas</span>
                            </span>
                        </span>
                        <span x-show="r.unread > 0"
                            class="shrink-0 inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-emerald-600 text-white text-[11px] font-bold"
                            x-text="r.unread"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Area percakapan --}}
        <div :class="aktif !== null ? 'flex' : 'hidden lg:flex'"
            class="flex-col flex-1 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Kepala percakapan --}}
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 shrink-0">
                <button type="button" @click="aktif = null" class="lg:hidden inline-flex items-center justify-center w-8 h-8 -ml-1 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <p class="truncate text-sm font-bold text-gray-800" x-text="aktif?.nama ?? 'Pilih ruangan'"></p>
                    <p class="text-xs text-gray-400" x-text="aktif?.tipe === 'global' ? 'Semua pengguna KasPro' : 'Pesan pribadi'"></p>
                </div>
            </div>

            {{-- Daftar pesan --}}
            <div class="relative flex-1 overflow-hidden">
                <div x-ref="pesanBox" @scroll="cekScroll()"
                    class="h-full overflow-y-auto px-4 py-4 space-y-3 bg-gray-50/60">
                    <template x-if="memuat">
                        <div class="h-full flex items-center justify-center text-sm text-gray-400">Memuat pesan…</div>
                    </template>

                    <div x-show="memuatLama" x-cloak class="flex justify-center py-1">
                        <span class="text-xs text-gray-400">Memuat pesan lama…</span>
                    </div>

                    <template x-for="m in pesanTampil()" :key="m.id">
                        <div>
                            <div x-show="m.pembagi"
                                class="flex justify-center py-1">
                                <span class="text-[11px] text-gray-500 bg-gray-200/70 px-3 py-1 rounded-full" x-text="m.label"></span>
                            </div>

                            <div x-show="!m.pembagi && m.moderated"
                                class="flex justify-center py-1">
                                <span class="text-xs text-gray-400 italic bg-gray-100 px-3 py-1 rounded-full">Pesan dihapus oleh moderator</span>
                            </div>

                            <div x-show="!m.pembagi && !m.moderated">
                                <div :class="m.own ? 'justify-end' : 'justify-start'"
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
                                            class="relative px-3 py-2 rounded-2xl shadow-sm">
                                            <p class="text-sm whitespace-pre-wrap break-words" x-text="m.body"></p>
                                            <p :class="m.own ? 'text-emerald-100' : 'text-gray-400'"
                                                class="text-[10px] mt-1 text-right leading-none">
                                                <span x-text="m.waktu"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <button x-show="!m.own"
                                        @click="laporkan(m)"
                                        class="shrink-0 text-[10px] text-gray-400 hover:text-red-600 mb-1"
                                        title="Laporkan pesan">Laporkan</button>
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

            {{-- Input pesan --}}
            <div x-show="aktif !== null" class="shrink-0 bg-white">
                <div class="flex items-center gap-0.5 px-3 pt-2 text-lg">
                    <template x-for="e in emojis" :key="e">
                        <button type="button" @click="tambahEmoji(e)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                            <span x-text="e"></span>
                        </button>
                    </template>
                </div>
                <form @submit.prevent="kirim()" class="flex items-end gap-2 p-3 border-t border-gray-100">
                    <textarea x-ref="inputPesan" x-model="body" rows="1" maxlength="2000" placeholder="Tulis pesan… (Enter untuk kirim)"
                        @keydown.enter.exact.prevent="kirim()"
                        @input="tumbuh($el)"
                        class="flex-1 resize-none max-h-32 overflow-y-auto text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    <button type="submit" :disabled="!body.trim()"
                        :class="body.trim() ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-200 cursor-not-allowed'"
                        class="inline-flex items-center justify-center h-10 w-10 rounded-lg text-white transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatApp', (rooms, pesan, aktifId, lastId) => ({
            rooms,
            aktif: null,
            pesan,
            lastId: Number(lastId) || 0,
            body: '',
            memuat: false,
            memuatLama: false,
            siap: pesan.length > 0,
            q: '',
            hasilCari: [],
            interval: null,
            pesanBaruCount: 0,
            emojis: ['😀', '😂', '❤️', '👍', '😮', '😢', '🔥', '✅'],

            init() {
                const idx = this.rooms.findIndex(r => r.id === aktifId);
                this.aktif = this.rooms[idx >= 0 ? idx : 0] ?? null;
                if (this.aktif && !this.siap) {
                    this.muatAwal();
                } else if (this.aktif) {
                    this.scrollBawah(true);
                }
                this.interval = setInterval(() => {
                    if (document.hidden) return;
                    this.sinkron(true);
                }, 3000);
            },

            pilihRuangan(r) {
                this.aktif = r;
                r.unread = 0;
                this.pesan = [];
                this.lastId = 0;
                this.siap = false;
                this.pesanBaruCount = 0;
                this.muatAwal();
            },

            async muatAwal() {
                if (!this.aktif) return;
                this.memuat = true;
                try {
                    const res = await fetch(this.aktif.urlPesan + '?after=0&mark=1');
                    const data = await res.json();
                    this.pesan = data;
                    this.lastId = data.length ? data[data.length - 1].id : 0;
                    this.siap = true;
                    this.pesanBaruCount = 0;
                    this.tandaiBacaRuangan();
                } catch (e) {
                } finally {
                    this.memuat = false;
                }
                this.scrollBawah(true);
            },

            async sinkron(mark) {
                if (!this.aktif || !this.siap) return;
                try {
                    const res = await fetch(this.aktif.urlPesan + '?after=' + this.lastId + (mark ? '&mark=1' : ''));
                    const data = await res.json();
                    if (data.length) {
                        const dekatBawah = this.diBawah();
                        const adaMilikSendiri = data.some(m => m.own);
                        this.pesan = this.pesan.concat(data);
                        this.lastId = data[data.length - 1].id;
                        this.tandaiBacaRuangan();
                        if (dekatBawah || adaMilikSendiri) {
                            this.pesanBaruCount = 0;
                            this.scrollBawah(true);
                        } else {
                            this.pesanBaruCount += data.length;
                        }
                    }
                } catch (e) {
                }
            },

            async muatLama() {
                if (!this.aktif || !this.siap || this.memuatLama || !this.pesan.length) return;
                const sebelum = this.$refs.pesanBox.scrollHeight;
                this.memuatLama = true;
                try {
                    const res = await fetch(this.aktif.urlPesan + '?before=' + this.pesan[0].id);
                    const data = await res.json();
                    if (data.length) {
                        const idsBaru = new Set(data.map(m => m.id));
                        this.pesan = data.concat(this.pesan.filter(m => !idsBaru.has(m.id)));
                        this.$nextTick(() => {
                            const el = this.$refs.pesanBox;
                            el.scrollTop = el.scrollHeight - sebelum;
                        });
                    }
                } catch (e) {
                } finally {
                    this.memuatLama = false;
                }
            },

            cekScroll() {
                const el = this.$refs.pesanBox;
                if (!el) return;
                if (el.scrollTop < 60) this.muatLama();
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 40 && this.pesanBaruCount > 0) {
                    this.pesanBaruCount = 0;
                }
            },

            bukaPesanBaru() {
                this.pesanBaruCount = 0;
                this.scrollBawah(true);
            },

            diBawah() {
                const el = this.$refs.pesanBox;
                if (!el) return true;
                return el.scrollHeight - el.scrollTop - el.clientHeight < 80;
            },

            tandaiBacaRuangan() {
                const r = (this.aktif || {}).id;
                if (r) {
                    const room = this.rooms.find(x => x.id === r);
                    if (room) room.unread = 0;
                }
            },

            async kirim() {
                const text = (this.body || '').trim();
                if (!text || !this.aktif) return;
                this.body = '';
                try {
                    const res = await fetch(this.aktif.urlKirim, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ body: text }),
                    });
                    if (res.status === 422) {
                        const j = await res.json();
                        alert(j.errors && j.errors.body ? j.errors.body[0] : 'Pesan gagal dikirim.');
                        return;
                    }
                    const j = await res.json();
                    this.pesan.push(j);
                    this.lastId = j.id;
                    this.pesanBaruCount = 0;
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

            async cari() {
                const q = (this.q || '').trim();
                if (q.length < 2) { this.hasilCari = []; return; }
                try {
                    const res = await fetch('{{ str_replace(url('/'), '', route('chat.cari')) }}?q=' + encodeURIComponent(q));
                    this.hasilCari = await res.json();
                } catch (e) { this.hasilCari = []; }
            },

            async bukaDm(u) {
                try {
                    const res = await fetch('{{ str_replace(url('/'), '', route('chat.dm.buka', ['user' => ':uid'])) }}'.replace(':uid', u.id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: '{}',
                    });
                    if (!res.ok) {
                        const j = await res.json();
                        alert(j.message || 'Gagal membuka percakapan.');
                        return;
                    }
                    const j = await res.json();
                    window.location.href = '{{ str_replace(url('/'), '', route('chat.index')) }}?room=' + j.room_id;
                } catch (e) {
                }
            },

            async laporkan(m) {
                const reason = window.prompt('Alasan melaporkan pesan ini (min. 10 karakter):');
                if (reason === null) return;
                if ((reason || '').trim().length < 10) { alert('Alasan minimal 10 karakter.'); return; }
                try {
                    await fetch('{{ str_replace(url('/'), '', route('chat.laporkan', ['message' => ':mid'])) }}'.replace(':mid', m.id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ reason: reason.trim() }),
                    });
                } catch (e) {
                }
            },

            pesanTampil() {
                const tampil = [];
                let kunciSebelum = null;
                for (const m of this.pesan) {
                    if (m.moderated && !m.own) {
                        tampil.push({
                            id: 'mod-' + m.id,
                            moderated: true,
                            pembagi: false,
                            body: 'Pesan dihapus oleh moderator',
                            own: m.own,
                        });
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
                return tampil;
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
                } catch (e) { return ''; }
            },

            scrollBawah(paksa = false) {
                const turun = () => {
                    const el = this.$refs.pesanBox;
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
</x-app-layout>