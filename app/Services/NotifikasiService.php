<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\NotifikasiDalamAplikasi;

class NotifikasiService
{
    public const TYPE_MENTION = 'chat.mention';

    public const TYPE_DM = 'chat.dm';

    public const TYPE_MODERATED = 'chat.moderated';

    public const TYPE_ADMIN = 'admin.message';

    public const TYPE_ACCOUNT_STATUS = 'account.status';

    public const TYPE_PLAN = 'plan.change';

    public const TYPE_DONATION = 'donation.status';

    /**
     * Kirim notifikasi dalam aplikasi kepada seorang pengguna.
     *
     * @param  array<string, mixed>  $data
     */
    public static function kirim(User|int $user, string $type, array $data = []): void
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);

        $user->notify(new NotifikasiDalamAplikasi($type, $data));
    }

    /**
     * Kirim notifikasi kepada semua akun aktif (pengumuman admin global).
     *
     * @param  array<string, mixed>  $data
     */
    public static function kirimKeSemua(string $type, array $data = []): void
    {
        User::query()
            ->where('status', 'aktif')
            ->select('id')
            ->chunkById(200, function ($users) use ($type, $data) {
                foreach ($users as $user) {
                    self::kirim($user->id, $type, $data);
                }
            });
    }
}
