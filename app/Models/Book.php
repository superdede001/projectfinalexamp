<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'category_id',
        'publisher',
        'publication_year_range',
        'availability_status',
        'store_location',
        'isbn',
    ];

    public function author() { return $this->belongsTo(Author::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function ratings() { return $this->hasMany(Rating::class); }

    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('score'), 2);
    }

    public function getStatusAttribute()
    {
        return $this->availability_status;
    }

    
}
