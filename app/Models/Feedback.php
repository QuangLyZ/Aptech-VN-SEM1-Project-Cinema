<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'context',
        'created_at',
    ];
}
