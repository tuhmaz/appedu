<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'category', 'keywords', 'meta_description','image', 'alt'];


    public function comments()
{
    return $this->morphMany(Comment::class, 'commentable');
}

}
