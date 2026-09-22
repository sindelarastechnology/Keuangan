<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatReport;
use App\Models\ChatRoom;
use App\Models\ChatRoomRead;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ChatService
{
    /**
     * Kirim pesan ke sebuah ruang (komunitas atau DM).
     *
     * $senderId boleh null untuk pesan sistem/admin (type TIPE_ADMIN).
     *
     * @throws ValidationException
     */
    public static function kirimPesan(?int $senderId, ChatRoom $room, string $body, string $type = ChatMessage::TIPE_USER): ChatMessage
    {
        $body = trim($body);

        if ($body === '') {
            throw ValidationException::withMessages(['body' => 'Pesan tidak boleh kosong.']);
        }

        if (mb_strlen($body) > 2000) {
            throw ValidationException::withMessages(['body' => 'Pesan maksimal 2000 karakter.']);
        }

        $message = ChatMessage::create([
            'room_id' => $room->id,
            'sender_id' => $senderId,
            'body' => $body,
            'type' => $type,
        ]);

        $room->touch();

        return $message;
    }

    public static function pesanBaru(ChatRoom $room, int $afterId = 0, int $limit = 50): Collection
    {
        return $room->messages()
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public static function pesanLama(ChatRoom $room, int $beforeId, int $limit = 50): Collection
    {
        $latestFirst = $room->messages()
            ->where('id', '<', $beforeId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $latestFirst->reverse();
    }

    public static function pesanTerakhir(ChatRoom $room, int $limit = 50): Collection
    {
        $latestFirst = $room->messages()
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $latestFirst->reverse();
    }

    /**
     * Daftar ruangan milik user: komunitas global + ruang DM.
     *
     * @return array<int, array{room: ChatRoom, nama: string, terakhir: ?ChatMessage, unread: int}>
     */
    public static function daftarRuangan(User $user): array
    {
        $daftar = [];

        $global = ChatRoom::ruangKomunitas();
        $daftar[] = self::metaRuangan($global, $user);

        $dmRooms = ChatRoom::where('tipe', ChatRoom::TIPE_DM)
            ->where(fn ($q) => $q->where('user_a_id', $user->id)->orWhere('user_b_id', $user->id))
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        foreach ($dmRooms as $room) {
            $daftar[] = self::metaRuangan($room, $user);
        }

        return $daftar;
    }

    /**
     * @return array{room: ChatRoom, nama: string, terakhir: ?ChatMessage, unread: int}
     */
    private static function metaRuangan(ChatRoom $room, User $user): array
    {
        $terakhir = $room->messages()->whereNull('moderated_at')->latest('id')->first();
        $posisiBaca = ChatRoomRead::posisiBaca($user->id, $room->id);
        $lastId = (int) ($terakhir?->id ?? 0);

        $unread = $lastId > 0 && $lastId > $posisiBaca
            ? (int) $room->messages()->whereNull('moderated_at')->where('id', '>', $posisiBaca)->count()
            : 0;

        return [
            'room' => $room,
            'nama' => $room->isGlobal() ? 'Komunitas KasPro' : optional($room->mitra($user->id))->name,
            'terakhir' => $terakhir,
            'unread' => $unread,
        ];
    }

    /**
     * Cek apakah pengguna berhak melihat sebuah ruang.
     */
    public static function dapatAkses(User $user, ChatRoom $room): bool
    {
        if ($room->isGlobal()) {
            return true;
        }

        return in_array($user->id, [(int) $room->user_a_id, (int) $room->user_b_id], true);
    }

    public static function tandaiBaca(User $user, ChatRoom $room): void
    {
        $lastId = (int) $room->messages()->max('id');

        if ($lastId > 0) {
            ChatRoomRead::tandaiBaca($user->id, $room->id, $lastId);
        }
    }

    /**
     * Total pesan belum dibaca untuk badge (komunitas + semua DM).
     */
    public static function totalBelumDibaca(User $user): int
    {
        return collect(self::daftarRuangan($user))->sum('unread');
    }

    /**
     * Laporkan pesan melanggar aturan.
     *
     * @throws ValidationException
     */
    public static function laporkan(User $reporter, ChatMessage $message, string $reason): ChatReport
    {
        if ($message->isModerated()) {
            throw ValidationException::withMessages(['reason' => 'Pesan telah dihapus moderator.']);
        }

        if ($message->sender_id === $reporter->id) {
            throw ValidationException::withMessages(['reason' => 'Tidak dapat melaporkan pesan sendiri.']);
        }

        return ChatReport::firstOrCreate(
            ['message_id' => $message->id, 'reporter_id' => $reporter->id],
            ['reason' => $reason, 'status' => ChatReport::STATUS_OPEN]
        );
    }

    /**
     * Moderasi (hapus) sebuah pesan dan beri tahu pengirimnya.
     *
     * @throws \InvalidArgumentException
     */
    public static function moderasi(ChatMessage $message, User $moderator, string $reason): void
    {
        if ($message->isModerated()) {
            throw new \InvalidArgumentException('Pesan sudah dimoderasi.');
        }

        $message->update([
            'moderated_at' => now(),
            'moderated_by' => $moderator->id,
            'moderated_reason' => $reason,
        ]);

        if ($message->sender_id && $message->sender_id !== $moderator->id) {
            NotifikasiService::kirim($message->sender_id, NotifikasiService::TYPE_MODERATED, [
                'title' => 'Pesan Anda dihapus moderator',
                'body' => 'Alasan: '.$reason,
            ]);
        }
    }

    /**
     * Cari akun aktif lain untuk memulai percakapan.
     */
    public static function cariUser(User $current, string $q): Collection
    {
        return User::query()
            ->where('status', 'aktif')
            ->where('id', '!=', $current->id)
            ->where(fn ($qq) => $qq->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email']);
    }
}
