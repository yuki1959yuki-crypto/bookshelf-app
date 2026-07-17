<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function likers()
    {
        return $this->belongsToMany(User::class, 'review_likes', 'review_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
