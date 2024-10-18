<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'album_id',
        'name',
        'email',
        'log',
        'date_add',
    ];

    protected $casts = [ 'date_add' => 'datetime' ];

    public function album() {
        return $this->belongsTo(Album::class);
    }
}
