<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PlanService
{
    public const FREE = 'free';

    public const PRO = 'pro';

    /**
     * Paket efektif pengguna (legate mengikuti masa berlaku paket Pro).
     */
    public static function plan(?User $user = null): string
    {
        $user ??= Auth::user();

        if (! $user) {
            return config('plans.default', self::FREE);
        }

        $plan = $user->plan ?: config('plans.default', self::FREE);

        if ($plan === self::PRO && $user->plan_expires_at && $user->plan_expires_at->isPast()) {
            return self::FREE;
        }

        return $plan;
    }

    public static function isPro(?User $user = null): bool
    {
        return self::plan($user) === self::PRO;
    }

    /**
     * Apakah paket user mengizinkan sebuah fitur?
     */
    public static function can(string $fitur, ?User $user = null): bool
    {
        $plan = self::plan($user);
        $hak = config("plans.hak.{$plan}");

        if ($hak === null) {
            return false;
        }

        if (! empty($hak['*'])) {
            return true;
        }

        return (bool) ($hak[$fitur] ?? false);
    }

    public static function nama(?User $user = null): string
    {
        $plan = self::plan($user);

        return config("plans.nama.{$plan}", $plan);
    }

    /**
     * Batas kuantitatif paket (barang, dsb.).
     */
    public static function batas(string $key, ?User $user = null): int|float
    {
        $plan = self::plan($user);
        $nilai = config("plans.batas.{$plan}.{$key}");

        return $nilai ?? PHP_INT_MAX;
    }

    /**
     * Fitur yang TIDAK aktif pada paket user (untuk halaman upgrade).
     *
     * @return array<int, string>
     */
    public static function fiturTerkunci(?User $user = null): array
    {
        $semua = array_keys(config('plans.fitur', []));

        return array_values(array_filter($semua, fn (string $f) => ! self::can($f, $user)));
    }

    /**
     * Saring daftar menu (nav-data.php) sesuai paket user.
     *
     * @param  array<int, array<string, mixed>>  $groups
     * @return array<int, array<string, mixed>>
     */
    public static function saringNav(array $groups): array
    {
        if (! Auth::check()) {
            return $groups;
        }

        return array_values(array_filter(
            array_map(static function (array $group): ?array {
                $items = self::saringNavItems($group['items'] ?? []);

                if ($items === []) {
                    return null;
                }

                $group['items'] = $items;

                return $group;
            }, $groups),
            fn ($group) => $group !== null
        ));
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function saringNavItems(array $items): array
    {
        return array_values(array_filter(
            array_map(static function (array $item): ?array {
                if (isset($item['feature']) && ! self::can($item['feature'])) {
                    return null;
                }

                if (isset($item['children'])) {
                    $children = self::saringNavItems($item['children']);

                    if ($children === []) {
                        return null;
                    }

                    $item['children'] = $children;
                }

                return $item;
            }, $items),
            fn ($item) => $item !== null
        ));
    }
}
