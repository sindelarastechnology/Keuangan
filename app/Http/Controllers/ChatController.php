<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    /**
     * Halaman chat: daftar ruangan (komunitas + DM) & percakapan aktif.
     */
    public function index(Request $request)
    {
        $ruangan = ChatService::daftarRuangan($request->user());

        $roomAktifId = (int) $request->integer('room');
        if ($roomAktifId === 0) {
            $roomAktifId = (int) ($ruangan[0]['room']->id ?? 0);
        }

        $pesanAwal = collect();
        $lastIdAwal = 0;

        if ($roomAktifId) {
            $roomAktif = ChatRoom::find($roomAktifId);

            if ($roomAktif && ChatService::dapatAkses($request->user(), $roomAktif)) {
                $pesanAwal = ChatService::pesanTerakhir($roomAktif, 50)
                    ->map(fn (ChatMessage $m) => $this->formatPesan($m, $request->user()->id))
                    ->values();
                $lastIdAwal = $pesanAwal->isNotEmpty() ? (int) $pesanAwal->last()['id'] : 0;
                ChatService::tandaiBaca($request->user(), $roomAktif);
            } else {
                $roomAktifId = 0;
            }
        }

        return view('chat.index', compact('ruangan', 'roomAktifId', 'pesanAwal', 'lastIdAwal'));
    }

    /**
     * Polling pesan baru (after = id pesan terakhir yang sudah dimiliki klien)
     * atau pesan lama (before = id pesan terlama yang sudah dimiliki klien).
     */
    public function pesan(Request $request, ChatRoom $room): JsonResponse
    {
        $user = $request->user();
        abort_unless(ChatService::dapatAkses($user, $room), 403);

        $beforeId = max(0, (int) $request->query('before', 0));

        $pesan = $beforeId > 0
            ? ChatService::pesanLama($room, $beforeId)
            : ChatService::pesanBaru($room, max(0, (int) $request->query('after', 0)));

        if ($beforeId === 0 && $request->query('mark') === '1') {
            ChatService::tandaiBaca($user, $room);
        }

        return response()->json($pesan->map(fn (ChatMessage $m) => $this->formatPesan($m, $user->id))->values());
    }

    /**
     * Kirim pesan ke ruang aktif.
     */
    public function kirim(Request $request, ChatRoom $room): JsonResponse
    {
        $user = $request->user();
        abort_unless(ChatService::dapatAkses($user, $room), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $pesan = ChatService::kirimPesan($user->id, $room, $validated['body']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        ChatService::tandaiBaca($user, $room);

        return response()->json($this->formatPesan($pesan, $user->id), 201);
    }

    /**
     * Cari akun aktif lain untuk memulai percakapan (minimal 2 karakter).
     */
    public function cari(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $users = ChatService::cariUser($request->user(), $q);

        return response()->json($users->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
        ])->values());
    }

    /**
     * Buka (atau buat) ruang DM dengan akun lain.
     */
    public function bukaDm(Request $request, User $target): JsonResponse
    {
        $current = $request->user();

        abort_if($target->id === $current->id, 422, 'Tidak dapat membuka chat dengan diri sendiri.');
        abort_unless($target->isAktif(), 422, 'Pengguna tersebut sudah tidak aktif.');

        $room = ChatRoom::buatDm($current->id, $target->id);

        return response()->json(['room_id' => $room->id]);
    }

    /**
     * Laporkan pesan yang dianggap melanggar.
     */
    public function laporkan(Request $request, ChatMessage $message): JsonResponse
    {
        $user = $request->user();
        abort_unless(ChatService::dapatAkses($user, $message->room), 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:255'],
        ]);

        try {
            ChatService::laporkan($user, $message, $validated['reason']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Total pesan belum dibaca untuk badge navbar (dipanggil polling).
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json(['total' => ChatService::totalBelumDibaca($request->user())]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatPesan(ChatMessage $m, int $userId): array
    {
        $admin = $m->type === ChatMessage::TIPE_ADMIN && ! $m->sender_id;
        $sender = $m->sender;

        return [
            'id' => (int) $m->id,
            'room_id' => (int) $m->room_id,
            'sender_id' => $m->sender_id,
            'sender_name' => $admin ? 'Admin' : $sender?->name,
            'sender_initial' => $admin ? 'A' : ($sender?->name ? mb_strtoupper(mb_substr($sender->name, 0, 1)) : '?'),
            'sender_color' => $admin ? 'bg-violet-600' : $this->senderWarna((int) $m->sender_id),
            'body' => $m->body,
            'type' => $m->type,
            'moderated' => $m->isModerated(),
            'moderated_reason' => $m->moderated_reason,
            'created_at' => $m->created_at?->toISOString(),
            'own' => (int) $m->sender_id === $userId,
        ];
    }

    /**
     * Warna avatar menentukan deterministik per pengirim (palet sama dengan Barang).
     */
    private function senderWarna(int $senderId): string
    {
        $palette = ['bg-emerald-600', 'bg-sky-600', 'bg-violet-600', 'bg-amber-600', 'bg-rose-600', 'bg-cyan-600', 'bg-indigo-600', 'bg-teal-600'];

        return $palette[$senderId % count($palette)];
    }
}
