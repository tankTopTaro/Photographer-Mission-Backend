<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Album extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'remote_id',
        'venue_id',
        'status',
        'date_add',
        'date_upd',
        'date_over',
    ];

    /**
     * Get the remote that owns the Album
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function remote() {
        return $this->belongsTo(Remote::class);
    }

    /**
     * Get the venue that owns the Album
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venue() {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the captures for the Album
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function captures() {
        return $this->hasMany(Capture::class);
    }

    /**
     * The users that are associated with the Album
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(User::class);
    }

    /**
     * The remotes that are associated with the Album
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function remotes() {
        return $this->belongsToMany(Remote::class, 'album_remote');
    }
}
