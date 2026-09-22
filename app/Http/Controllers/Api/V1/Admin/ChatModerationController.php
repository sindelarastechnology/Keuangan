<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatReport;
use App\Models\ChatRoom;
use App\Services\ChatService;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatModerationController extends Controller
{
    /**
     * Antrean laporan pesan yang masih terbuka (moderasi oleh admin app).
     */
    public function index(Request $request): JsonResponse
    {
        $reports = ChatReport::with(['message.room', 'message.sender', 'reporter'])
            ->where('status', ChatReport::STATUS_OPEN)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'message' => 'OK',
            'data' => collect($reports->items())->map(fn (ChatReport $r) => [
                'id' => $r->id,
                'status' => $r->status,
                'reason' => $r->reason,
                'created_at' => $r->created_at?->toISOString(),
                'reporter' => $r->reporter ? ['id' => $r->reporter->id, 'name' => $r->reporter->name] : null,
                'message' => $r->message ? [
                    'id' => $r->message->id,
                    'body' => $r->message->body,
                    'sender' => $r->message->sender?->name,
                    'moderated' => $r->message->isModerated(),
                ] : null,
            ])->values(),
            'meta' => ['total' => $reports->total()],
        ]);
    }

    /**
     * Daftar ruangan chat (DM komunitas & individu) untuk monitoring.
     */
    public function rooms(Request $request): JsonResponse
    {
        $rooms = ChatRoom::query()
            ->orderByDesc('updated_at')
            ->limit($request->integer('limit', 100))
            ->get();

        return response()->json([
            'message' => 'OK',
            'data' => $rooms->map(fn (ChatRoom $room) => [
                'id' => $room->id,
                'tipe' => $room->tipe,
                'user_a' => $room->userA ? ['id' => $room->userA->id, 'name' => $room->userA->name] : null,
                'user_b' => $room->userB ? ['id' => $room->userB->id, 'name' => $room->userB->name] : null,
                'terakhir_pesan' => $room->updated_at?->toISOString(),
            ])->values(),
        ]);
    }

    /**
     * Pesan dalam satu ruangan (dengan flag moderasi).
     */
    public function messages(Request $request, int $room): JsonResponse
    {
        $chatRoom = ChatRoom::findOrFail($room);

        $messages = ChatService::pesanBaru($chatRoom, $request->integer('after', 0))
            ->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'sender' => $m->type === ChatMessage::TIPE_ADMIN ? 'Admin' : $m->sender?->name,
                'type' => $m->type,
                'body' => $m->body,
                'moderated' => $m->isModerated(),
                'moderated_at' => $m->moderated_at?->toISOString(),
                'created_at' => $m->created_at?->toISOString(),
            ])->values();

        return response()->json(['message' => 'OK', 'data' => $messages]);
    }

    /**
     * Kirim pesan atas nama admin ke sebuah ruang (komunitas atau DM).
     *
     * Pesan tersimpan dengan type=admin, sender_id=null, dan ditampilkan
     * berlabel "Admin" di sisi web. Untuk ruang DM, kedua anggota diberi
     * notifikasi in-app (type admin.message).
     */
    public function send(Request $request, int $room): JsonResponse
    {
        $chatRoom = ChatRoom::findOrFail($room);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $pesan = ChatService::kirimPesan(null, $chatRoom, $validated['body'], ChatMessage::TIPE_ADMIN);

        if ($chatRoom->isDm()) {
            foreach (array_unique([$chatRoom->user_a_id, $chatRoom->user_b_id]) as $memberId) {
                if ($memberId) {
                    NotifikasiService::kirim($memberId, NotifikasiService::TYPE_ADMIN, [
                        'title' => 'Pesan dari Admin',
                        'body' => mb_strimwidth($validated['body'], 0, 140, '…'),
                        'room_id' => (int) $chatRoom->id,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Pesan admin terkirim.',
            'data' => [
                'id' => (int) $pesan->id,
                'room_id' => (int) $pesan->room_id,
                'type' => $pesan->type,
                'body' => $pesan->body,
                'created_at' => $pesan->created_at?->toISOString(),
            ],
        ], 201);
    }

    /**
     * Moderasi satu pesan (hilangkan dari hadapan publik + catat moderator).
     */
    public function moderate(Request $request, ChatMessage $message): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:255'],
        ]);

        try {
            ChatService::moderasi($message, $request->user(), $validated['reason']);
        } catch (\Throwable $e) {
            return response()->json(['errors' => ['body' => [$e->getMessage()]]], 422);
        }

        return response()->json(['message' => 'Pesan dimoderasi.', 'data' => ['id' => $message->id]]);
    }

    /**
     * Tutup laporan setelah ditangani (dianggap selesai).
     */
    public function resolve(Request $request, ChatReport $report): JsonResponse
    {
        $report->forceFill(['status' => ChatReport::STATUS_CLOSED])
            ->save();

        return response()->json(['message' => 'Laporan ditutup.', 'data' => ['id' => $report->id]]);
    }
}
