<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Showtime extends Model
{
    protected $table = 'showtimes';

    public $timestamps = false;

    protected $fillable = [
        'movie_id',
        'room_id',
        'subtitle_id',
        'start_time',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
