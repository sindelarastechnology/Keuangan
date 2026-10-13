<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

/**
 * Notifikasi database yang disimpan pada koneksi shared (MySQL).
 *
 * Model DatabaseNotification bawaan Laravel tidak memiliki koneksi tetap.
 * Ketika default connection diarahkan ke database tenant per-request, model
 * ini menjaga tabel `notifications` tetap berada di database shared.
 */
class SharedNotification extends DatabaseNotification
{
    protected $connection = 'mysql';
}
