<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use BelongsToUser;

    protected $table = 'pengaturan';

    protected $fillable = ['key', 'value'];

    public static function atur(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function tampil(string $key, $default = null)
    {
        $row = self::where('key', $key)->first();

        return $row?->value ?? $default;
    }
}
