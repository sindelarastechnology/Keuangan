<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotifyController extends Controller
{
    /**
     * Daftar notifikasi in-app milik satu akun (kanvas Flutter).
     */
    public function index(Request $request, User $user): JsonResponse
    {
        $notifikasi = $user->notifications()
            ->orderByDesc('created_at')
            ->take($request->integer('limit', 50))
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $n->data,
                'read_at' => $n->read_at?->toISOString(),
                'created_at' => $n->created_at?->toISOString(),
            ])
            ->values();

        return response()->json([
            'message' => 'OK',
            'data' => [
                'items' => $notifikasi,
                'unread' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    /**
     * Kirim notifikasi in-app (broadcast ke semua, atau ke satu akun).
     *
     * Body: { "title": "...", "body": "...", "type"?: "...", "user_id"?: int }.
     * Tanpa user_id → kirim ke semua pengguna aktif (termasuk admin).
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
            'type' => ['sometimes', 'string', 'max:60'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $data = [
            'title' => $validated['title'],
            'body' => $validated['body'],
            'type' => $validated['type'] ?? NotifikasiService::TYPE_ADMIN,
        ];

        try {
            if (! empty($validated['user_id'])) {
                NotifikasiService::kirim($validated['user_id'], $data['type'], $data);
            } else {
                NotifikasiService::kirimKeSemua($data['type'], $data);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        return response()->json([
            'message' => 'Notifikasi terkirim.',
            'data' => ['target' => empty($validated['user_id']) ? 'all' : 'single'],
        ], 201);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca (dari kanvas Flutter).
     */
    public function markRead(Request $request, User $user, string $notification): JsonResponse
    {
        $item = $user->notifications()->whereKey($notification)->first();
        abort_unless($item, 404);

        $item->markAsRead();

        return response()->json(['message' => 'OK']);
    }
}
