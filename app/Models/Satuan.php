<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Database\Factories\SatuanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use BelongsToUser;

    /** @use HasFactory<SatuanFactory> */
    use HasFactory;

    protected $table = 'satuan';

    protected $fillable = ['nama'];
}
