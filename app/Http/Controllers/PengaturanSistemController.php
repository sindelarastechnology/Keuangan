<?php

namespace App\Http\Controllers;

use App\Models\AkunPerkiraan;
use App\Models\Gudang;
use App\Models\Pengaturan;
use App\Models\StokGudang;
use App\Services\PengaturanSistemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PengaturanSistemController extends Controller
{
    public function index()
    {
        $katalog = PengaturanSistemService::katalogAkunPenting();
        $jenisLabel = AkunPerkiraan::listJenis();

        $roleList = [];

        foreach ($katalog as $role => $def) {
            $roleList[] = [
                'role' => $role,
                'label' => $def['label'],
                'jenis' => $def['jenis'][0],
                'jenisLabel' => $jenisLabel[$def['jenis'][0]],
                'kodeDefault' => $def['kode'],
                'savedId' => (int) Pengaturan::tampil(PengaturanSistemService::keyAkun($role), 0),
                'resolvedId' => PengaturanSistemService::akunId($role),
            ];
        }

        $akunOptions = [];

        foreach ($jenisLabel as $jenis => $label) {
            $akunOptions[$jenis] = AkunPerkiraan::aktif()->leaf()
                ->where('jenis', $jenis)
                ->orderBy('kode')
                ->get()
                ->map(fn (AkunPerkiraan $akun) => ['id' => $akun->id, 'label' => $akun->kode.' - '.$akun->nama]);
        }

        $resolved = [];

        foreach (array_keys($katalog) as $role) {
            $id = PengaturanSistemService::akunId($role);
            $resolved[$role] = $id ? AkunPerkiraan::find($id) : null;
        }

        $gudangList = Gudang::aktif()->orderBy('nama')->get();
        $gudangUtamaId = Gudang::utama()?->id;
        $gudangPembelian = (int) Pengaturan::tampil('gudang_default_pembelian', 0);
        $gudangPenjualan = (int) Pengaturan::tampil('gudang_default_penjualan', 0);
        $gudangStok = StokGudang::selectRaw('gudang_id, count(*) as jumlah_item, sum(qty) as total_qty')
            ->where('qty', '>', 0)
            ->groupBy('gudang_id')
            ->pluck('jumlah_item', 'gudang_id')
            ->all();

        return view('pengaturan.sistem', compact(
            'roleList',
            'akunOptions',
            'resolved',
            'gudangList',
            'gudangUtamaId',
            'gudangPembelian',
            'gudangPenjualan',
            'gudangStok'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $katalog = PengaturanSistemService::katalogAkunPenting();

        foreach ($request->all() as $key => $value) {
            if (! str_starts_with($key, 'akun_')) {
                continue;
            }

            $role = substr($key, 5);

            if (! isset($katalog[$role])) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            if (! AkunPerkiraan::aktif()->leaf()->whereKey($value)->exists()) {
                return back()->withInput()->withErrors(['akun_'.$role => 'Akun yang dipilih tidak valid.']);
            }
        }

        foreach (['gudang_default_pembelian', 'gudang_default_penjualan'] as $key) {
            $value = $request->input($key);

            if ($value !== null && $value !== '') {
                if (! Gudang::aktif()->whereKey($value)->exists()) {
                    return back()->withInput()->withErrors([$key => 'Gudang yang dipilih tidak valid.']);
                }
            }
        }

        foreach ($request->all() as $key => $value) {
            if ($key === '_token' || $key === 'tab') {
                continue;
            }

            $dikenal = str_starts_with($key, 'akun_') && isset($katalog[substr($key, 5)]);
            $dikenal = $dikenal || in_array($key, ['gudang_default_pembelian', 'gudang_default_penjualan'], true);

            if (! $dikenal) {
                continue;
            }

            if ($value === null || $value === '') {
                Pengaturan::where('key', $key)->delete();

                continue;
            }

            Pengaturan::atur($key, $value);
        }

        $tab = in_array($request->input('tab'), ['akun', 'gudang'], true) ? $request->input('tab') : 'akun';

        return redirect()->route('pengaturan.sistem.index', ['tab' => $tab])
            ->with('success', 'Pengaturan Sistem berhasil disimpan.');
    }
}
