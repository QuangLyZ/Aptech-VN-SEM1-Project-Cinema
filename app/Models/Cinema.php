<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    protected $table = 'cinemas';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
    ];
}
