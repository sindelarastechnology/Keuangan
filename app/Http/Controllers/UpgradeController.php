<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\User;
use App\Services\NotifikasiService;
use App\Services\PlanService;
use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    /**
     * Halaman paket berlangganan & status paket user saat ini.
     */
    public function index(Request $request)
    {
        $paket = PlanService::plan();
        $fitur = (array) config('plans.fitur', []);
        $terkunci = PlanService::fiturTerkunci();
        $sisaBarang = null;

        if (! PlanService::isPro()) {
            $batas = (int) PlanService::batas('barang');
            $terpakai = Barang::count();
            $sisaBarang = max(0, $batas - $terpakai);
        }

        return view('upgrade.index', compact('paket', 'fitur', 'terkunci', 'sisaBarang'));
    }

    /**
     * Kirim permintaan upgrade ke akun admin yang dikonfigurasi.
     */
    public function kirimPermintaan(Request $request)
    {
        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $catatan = trim((string) ($validated['catatan'] ?? ''));
        $emails = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) config('plans.email_admin', ''))
        )));

        if ($emails === []) {
            $emails = ['admin@keuangan.test'];
        }

        foreach ($emails as $email) {
            $admin = User::where('status', 'aktif')->where('email', $email)->first();

            if (! $admin) {
                continue;
            }

            NotifikasiService::kirim($admin, NotifikasiService::TYPE_ADMIN, [
                'title' => 'Permintaan upgrade paket Pro',
                'body' => $request->user()->name.' ('.$request->user()->email.') meminta upgrade.'
                    .($catatan !== '' ? ' Catatan: '.$catatan : ''),
                'user_id' => $request->user()->id,
            ]);
        }

        return redirect()->route('upgrade.index')
            ->with('success', 'Permintaan upgrade Pro telah diterima. Tim KasPro akan segera memproses.');
    }
}
