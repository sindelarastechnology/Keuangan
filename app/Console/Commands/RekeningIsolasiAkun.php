<?php

namespace App\Console\Commands;

use App\Models\AkunPerkiraan;
use App\Models\JurnalUmum;
use App\Models\Rekening;
use App\Models\User;
use App\Services\JournalService;
use App\Services\PengaturanSistemService;
use App\Services\TenantDatabase;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Signature('rekening:isolasi-akun')]
#[Description('Isolasi akun perkiraan antar rekening agar setiap rekening (kas/bank) memakai akun GL sendiri dan saldonya tidak saling tercampur.')]
class RekeningIsolasiAkun extends Command
{
    /**
     * Execute the console command.
     *
     * Untuk setiap tenant, bila ada akun kas/bank yang dipakai oleh lebih dari
     * satu rekening, rekening lain (selain yang paling awal) dipindah ke akun
     * perkiraan baru turunan akun tersebut. Jurnal "Saldo Awal" milik rekening
     * yang dipindah di-void pada akun bersama, lalu dicatat ulang pada akun baru.
     */
    public function handle(): int
    {
        if (User::query()->count() === 0) {
            $this->info('Tidak ada user untuk diproses.');

            return self::SUCCESS;
        }

        foreach (User::query()->orderBy('id')->get() as $user) {
            if (TenantDatabase::enabled()) {
                TenantDatabase::connect($user);
            }

            try {
                Auth::guard('web')->setUser($user);

                $this->prosesUser($user);
            } finally {
                if (TenantDatabase::enabled()) {
                    TenantDatabase::disconnect();
                }
            }
        }

        $this->info('Selesai. Silakan buka halaman Rekening untuk memverifikasi saldo tiap rekening.');

        return self::SUCCESS;
    }

    /**
     * Isolasi akun untuk data satu user pada koneksi aktif.
     */
    private function prosesUser(User $user): void
    {
        $rekenings = Rekening::query()->orderBy('id')->get();

        if ($rekenings->isEmpty()) {
            $this->line("User {$user->email}: tidak ada rekening.");

            return;
        }

        $bercampur = $rekenings->groupBy('akun_id')->filter(fn ($g) => $g->count() > 1);

        if ($bercampur->isEmpty()) {
            $this->info("User {$user->email}: semua rekening sudah memakai akun masing-masing.");

            return;
        }

        $this->warn('User '.$user->email.': menemukan akun yang dipakai beberapa rekening.');

        foreach ($bercampur as $akunId => $kelompok) {
            $akunBersama = AkunPerkiraan::find($akunId);

            if (! $akunBersama) {
                $this->error("Akun #{$akunId} tidak ditemukan.");

                continue;
            }

            $urutan = $kelompok->sortBy('id')->values();
            $pemegang = $urutan->first();
            $tersangkut = $urutan->slice(1);

            $this->info("Akun {$akunBersama->kode} {$akunBersama->nama} dipakai {$urutan->count()} rekening; "
                ."{$pemegang->nama} tetap memakai akun ini.");

            foreach ($tersangkut as $rekening) {
                $this->isolasiRekening($rekening, $akunBersama);
            }
        }
    }

    private function isolasiRekening(Rekening $rekening, AkunPerkiraan $akunBersama): void
    {
        DB::beginTransaction();

        try {
            foreach ($this->jurnalSaldoAwal($rekening) as $jurnal) {
                JournalService::void($jurnal, 'Pemindahan akun rekening');
            }

            $akunBaru = $this->buatAkunBaru($akunBersama, $rekening);
            $rekening->update(['akun_id' => $akunBaru->id]);

            if ((float) $rekening->saldo_awal > 0 && $this->jurnalSaldoAwal($rekening)->isEmpty()) {
                $akunModal = PengaturanSistemService::akunId('modal');

                if ($akunModal) {
                    JournalService::post(
                        'manual',
                        now()->toDateString(),
                        [
                            ['akun_id' => $rekening->akun_id, 'debit' => (float) $rekening->saldo_awal, 'kredit' => 0, 'keterangan' => 'Saldo Awal '.$rekening->nama],
                            ['akun_id' => $akunModal, 'debit' => 0, 'kredit' => (float) $rekening->saldo_awal, 'keterangan' => 'Setoran Modal Awal Rekening '.$rekening->nama],
                        ],
                        'Saldo Awal Rekening '.$rekening->nama,
                        $rekening
                    );
                }
            }

            DB::commit();

            $this->info("Rekening '{$rekening->nama}' dipindah ke akun baru {$akunBaru->kode} {$akunBaru->nama}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Gagal memproses rekening '{$rekening->nama}': {$e->getMessage()} (asal {$akunBersama->kode}).");
        }
    }

    private function jurnalSaldoAwal(Rekening $rekening)
    {
        return JurnalUmum::where('ref_type', Rekening::class)
            ->where('ref_id', $rekening->id)
            ->where('keterangan', 'like', 'Saldo Awal%')
            ->where('keterangan', 'not like', '[DIBATALKAN]%')
            ->orderBy('id')
            ->get();
    }

    private function buatAkunBaru(AkunPerkiraan $akunBersama, Rekening $rekening): AkunPerkiraan
    {
        $awalan = $rekening->jenis === 'bank' ? 'Bank' : 'Kas';
        $nama = $awalan.' '.$rekening->nama;
        $parentId = $akunBersama->parent_id ?: AkunPerkiraan::query()
            ->where('kode', substr($akunBersama->kode, 0, -1))
            ->value('id');

        $n = 1;

        do {
            $kode = $akunBersama->kode.$n;
            $n++;
        } while (AkunPerkiraan::query()->where('kode', $kode)->exists());

        return AkunPerkiraan::create([
            'kode' => $kode,
            'nama' => $nama,
            'jenis' => 'aset',
            'saldo_normal' => 'debit',
            'is_header' => false,
            'parent_id' => $parentId,
            'keterangan' => 'Dibuat otomatis untuk rekening '.$rekening->nama,
            'is_aktif' => true,
        ]);
    }
}
