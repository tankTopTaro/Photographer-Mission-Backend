<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remote extends Model
{
    use HasFactory;

    protected $fillable = [ 'venue_id' ];

    /**
     * The venue that this remote is associated with
     *
     * @return BelongsTo
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the albums that belong to the Remote
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_remote');
    }
}
