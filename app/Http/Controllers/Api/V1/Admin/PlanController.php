<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotifikasiService;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Manajemen paket langganan via API admin (Flutter).
 *
 * Seluruh endpoint dijamin 'auth.integration' (middleware yang sama dengan
 * user-manajemen API) — admin app menghubungi backend dengan X-Integration-Key.
 */
class PlanController extends Controller
{
    /**
     * Daftar paket yang tersedia (metadata untuk kanvas UI upgrade).
     */
    public function index(Request $request): JsonResponse
    {
        $user = User::find($request->input('user_id'));

        return response()->json([
            'message' => 'OK',
            'data' => [
                'paket' => [
                    'free' => $this->ringkasPaket('free'),
                    'pro' => $this->ringkasPaket('pro'),
                ],
                'user' => $user ? [
                    'id' => $user->id,
                    'plan' => PlanService::plan($user),
                    'is_pro' => PlanService::isPro($user),
                    'trial_ends_at' => $user->trial_ends_at?->toISOString(),
                    'plan_expires_at' => $user->plan_expires_at?->toISOString(),
                ] : null,
            ],
        ]);
    }

    /**
     * Set/ubah paket akun secara langsung (dipicu admin/Flutter).
     */
    public function setPlan(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'plan' => ['required', Rule::in(['free', 'pro'])],
            'plan_expires_at' => ['nullable', 'date'],
            'trial_ends_at' => ['nullable', 'date'],
        ]);

        $user->forceFill([
            'plan' => $validated['plan'],
            'plan_expires_at' => $validated['plan_expires_at'] ?? null,
            'trial_ends_at' => $validated['trial_ends_at'] ?? null,
        ])->save();

        // Catat ke notifikasi: pengguna tahu paketnya berubah.
        NotifikasiService::kirim($user, NotifikasiService::TYPE_PLAN, [
            'title' => 'Paket langganan diperbarui',
            'body' => 'Paket Anda kini: '.($validated['plan'] === 'pro' ? 'Pro' : 'Gratis').'.',
        ]);

        return response()->json([
            'message' => 'Paket akun diperbarui.',
            'data' => [
                'id' => $user->id,
                'plan' => $user->plan,
                'is_pro' => PlanService::isPro($user),
                'plan_expires_at' => $user->plan_expires_at?->toISOString(),
                'trial_ends_at' => $user->trial_ends_at?->toISOString(),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function ringkasPaket(string $nama): array
    {
        return [
            'nama' => config("plans.nama.{$nama}", $nama),
            'batas_barang' => config("plans.batas.{$nama}.barang", PHP_INT_MAX),
        ];
    }
}
