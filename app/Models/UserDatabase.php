<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pemetaan user ke file database tenant (SQLite) miliknya.
 *
 * Disimpan pada database shared; tidak ikut default connection tenant.
 */
class UserDatabase extends Model
{
    public const STATUS_PROVISIONING = 'provisioning';

    public const STATUS_READY = 'ready';

    public const STATUS_FAILED = 'failed';

    protected $connection = 'mysql';

    protected $fillable = [
        'user_id',
        'database_path',
        'status',
        'provisioned_at',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            'provisioned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
