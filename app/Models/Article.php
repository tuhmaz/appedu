<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Article extends Model
{
    use HasFactory;

    protected $fillable = ['grade_level', 'subject_id', 'semester_id', 'title', 'content', 'meta_description', 'author_id', 'visit_count',];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'grade_level', 'grade_name');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable'); // Use default mysql connection for comments
    }


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // في موديل Article

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'article_keyword', 'article_id', 'keyword_id');
    }


}
