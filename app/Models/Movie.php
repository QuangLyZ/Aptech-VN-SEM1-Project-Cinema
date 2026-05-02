<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $casts = [
        'release_date' => 'date',
    ];

    protected $fillable = [
        'name',
        'genre',
        'duration',
        'release_date',
        'director',
        'description',
        'poster',
        'actors',
        'age_limit',
        'trailer_link',
    ];

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        // GRACE: Tối ưu cực độ - Sử dụng giá trị đã load trước (Eager Loading) nếu có
        $count = $this->reviews_count ?? $this->reviews()->where('is_visible', true)->count();
        $sum = $this->reviews_sum_rating ?? $this->reviews()->where('is_visible', true)->sum('rating');
        
        // Công thức: (Tổng điểm + 5 điểm nền) / (Số lượt đánh giá + 1)
        return round(($sum + 5) / ($count + 1), 1);
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews_count ?? $this->reviews()->where('is_visible', true)->count();
    }
}
