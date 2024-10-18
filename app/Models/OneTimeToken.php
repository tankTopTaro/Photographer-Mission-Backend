<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'email',
        'token',
        'used'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
