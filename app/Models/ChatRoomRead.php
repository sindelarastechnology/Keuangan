<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomRead extends Model
{
    /**
     * Pasangan (user_id, room_id) sebagai kunci; tidak memakai auto-increment.
     */
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'room_id',
        'last_read_message_id',
    ];

    /**
     * Simpan posisi baca terakhir untuk user pada ruang tertentu.
     */
    public static function tandaiBaca(int $userId, int $roomId, int $lastMessageId): void
    {
        self::query()->upsert(
            [['user_id' => $userId, 'room_id' => $roomId, 'last_read_message_id' => $lastMessageId]],
            ['user_id', 'room_id'],
            ['last_read_message_id', 'updated_at']
        );
    }

    public static function posisiBaca(int $userId, int $roomId): int
    {
        return (int) self::where('user_id', $userId)->where('room_id', $roomId)->value('last_read_message_id');
    }
}
