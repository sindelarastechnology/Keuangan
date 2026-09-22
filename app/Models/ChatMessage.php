<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatMessage extends Model
{
    public const TIPE_USER = 'user';

    public const TIPE_ADMIN = 'admin';

    protected $fillable = [
        'room_id',
        'sender_id',
        'body',
        'type',
        'moderated_by',
        'moderated_at',
        'moderated_reason',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function report(): HasOne
    {
        return $this->hasOne(ChatReport::class, 'message_id');
    }

    public function isModerated(): bool
    {
        return $this->moderated_at !== null;
    }
}
