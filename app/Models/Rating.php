<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blog_id',
        'rating',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function user() {
        return $this->belongsto(User::class);
    }
}
