<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['author', 'categories'];
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    public function averageRating()
    {
        return $this->ratings()->avg('score');
    }
    public function totalVoters()
    {
        return $this->ratings()->count();
    }
    public function getStatusColorAttribute()
    {
        return [
            'available' => 'text-green-500',
            'Rented' => 'text-red-500',
            'Reserved' => 'text-yellow-500',
        ][$this->availability_status] ?? 'text-gray-500';
    }
}
