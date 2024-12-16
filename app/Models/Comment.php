<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

class Comment extends Model
{
    // استخدام قاعدة البيانات الرئيسية دائماً
    protected $connection = 'jo';
    
    protected $fillable = ['body', 'commentable_id', 'commentable_type', 'user_id', 'connection_name'];

    protected static function boot()
    {
        parent::boot();
        
        // Global scope لتصفية التعليقات حسب الاتصال الحالي
        static::addGlobalScope('connection_filter', function (Builder $builder) {
            $connection = session('database', 'jo');
            $builder->where('connection_name', $connection);
        });
    }

    // العلاقة متعددة الأشكال
    public function commentable()
    {
        return $this->morphTo();
    }

    // العلاقة مع المستخدم (من قاعدة البيانات الرئيسية)
    public function user()
    {
        // تعيين نموذج User ليستخدم قاعدة البيانات الرئيسية
        $userModel = new User();
        $userModel->setConnection('jo');
        
        return $this->belongsTo(get_class($userModel), 'user_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
