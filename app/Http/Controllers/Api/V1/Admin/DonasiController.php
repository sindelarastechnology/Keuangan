<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Donasi;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Manajemen donasi via API admin (Flutter).
 *
 * Seluruh endpoint dijamin 'auth.integration' (middleware yang sama dengan
 * user-manajemen API) — admin app menghubungi backend dengan X-Integration-Key.
 */
class DonasiController extends AdminController
{
    /**
     * Daftar donasi (dari seluruh pengguna). Mendukung filter status & pencarian.
     */
    public function index(Request $request)
    {
        $status = (string) $request->query('status', '');
        $q = trim((string) $request->query('q', ''));

        $donasi = Donasi::query()
            ->with('user:id,name,email')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($q !== '', fn ($query) => $query->whereHas('user', function ($user) use ($q) {
                $user->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            }))
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'message' => 'OK',
            'data' => collect($donasi->items())->map(fn (Donasi $d) => $this->ringkasDonasi($d))->values(),
            'meta' => [
                'current_page' => $donasi->currentPage(),
                'last_page' => $donasi->lastPage(),
                'per_page' => $donasi->perPage(),
                'total' => $donasi->total(),
            ],
        ]);
    }

    /**
     * Detail satu donasi.
     */
    public function show(Request $request, Donasi $donasi)
    {
        $donasi->load('user:id,name,email');

        return $this->ok($this->ringkasDonasi($donasi));
    }

    /**
     * Ubah status verifikasi donasi (pending | confirmed).
     */
    public function status(Request $request, Donasi $donasi)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Donasi::STATUSES)],
        ]);

        $donasi->status = $validated['status'];
        $donasi->save();

        if ($donasi->isConfirmed()) {
            NotifikasiService::kirim($donasi->user_id, NotifikasiService::TYPE_DONATION, [
                'title' => 'Donasi terkonfirmasi',
                'body' => 'Terima kasih! Donasi Anda telah dikonfirmasi oleh pengelola.',
                'donasi_id' => $donasi->id,
            ]);
        }

        return $this->ok($this->ringkasDonasi($donasi));
    }

    /**
     * @return array<string, mixed>
     */
    private function ringkasDonasi(Donasi $donasi): array
    {
        return [
            'id' => (int) $donasi->id,
            'user' => $donasi->user ? [
                'id' => (int) $donasi->user->id,
                'name' => $donasi->user->name,
                'email' => $donasi->user->email,
            ] : null,
            'nominal' => $donasi->nominal !== null ? (float) $donasi->nominal : null,
            'keterangan' => $donasi->keterangan,
            'status' => $donasi->status,
            'bukti_url' => $donasi->bukti_path !== null
                ? url(Storage::url($donasi->bukti_path))
                : null,
            'created_at' => $donasi->created_at?->toISOString(),
            'updated_at' => $donasi->updated_at?->toISOString(),
        ];
    }
}
