<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonasiController extends Controller
{
    /**
     * Halaman donasi: QRIS, form donasi seikhlasnya, dan riwayat donasi user.
     */
    public function index(Request $request): View
    {
        $riwayat = Donasi::query()
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('donasi.index', ['riwayat' => $riwayat]);
    }

    /**
     * Simpan donasi. Semua kolom opsional (nominal, bukti, keterangan).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nominal' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $data = [
            'nominal' => (filled($validated['nominal'] ?? null))
                ? (float) $validated['nominal']
                : null,
            'keterangan' => trim((string) ($validated['keterangan'] ?? '')) ?: null,
            'status' => Donasi::STATUS_PENDING,
        ];

        if ($request->hasFile('bukti')) {
            $data['bukti_path'] = $request->file('bukti')->store('bukti-donasi', 'public');
        }

        Donasi::create($data);

        return redirect()->route('donasi.index')
            ->with('success', 'Terima kasih atas donasi Anda. Bukti akan kami verifikasi.');
    }
}
