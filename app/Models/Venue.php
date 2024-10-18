<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [ 'name' ];

    /**
     * Get the remotes that belong to the venue.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function remote()
    {
        return $this->hasMany(Remote::class);
    }

    /**
     * Get the photobooths that belong to the venue.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photobooths()
    {
        return $this->hasMany(Photobooth::class);
    }
}
