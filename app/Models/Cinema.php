<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cinema extends Model
{
    protected $fillable = [
        'name',
        'address',
        'district',
        'phone',
        'hours',
        'screens',
        'seats',
        'features',
        'image',
        'map',
        'status',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
    protected $casts = [
        'features' => 'array',
    ];
    public function showtimes()
    {
        return $this->hasManyThrough(Showtime::class, Room::class);
    }
}
