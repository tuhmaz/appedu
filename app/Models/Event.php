<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_time',
        'location',
        'status'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime'
    ];

    protected $dates = [
        'event_date',
        'event_time'
    ];

    // التأكد من أن الحالة الافتراضية هي 'upcoming'
    protected $attributes = [
        'status' => 'upcoming'
    ];

    // Scope للأحداث القادمة
    public function scopeUpcoming($query)
    {
        return $query->whereDate('event_date', '>=', now())
                    ->orderBy('event_date', 'asc');
    }

    // Scope للأحداث المنتهية
    public function scopePast($query)
    {
        return $query->whereDate('event_date', '<', now())
                    ->orderBy('event_date', 'desc');
    }

    // تحويل الوقت قبل الحفظ
    public function setEventTimeAttribute($value)
    {
        if (!empty($value)) {
            try {
                // تنظيف القيمة من أي بيانات إضافية
                $time = trim($value);
                // التحقق من صحة تنسيق الوقت
                if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
                    $this->attributes['event_time'] = $time;
                } else {
                    $this->attributes['event_time'] = null;
                }
            } catch (\Exception $e) {
                $this->attributes['event_time'] = null;
            }
        } else {
            $this->attributes['event_time'] = null;
        }
    }

    // الحصول على الوقت بتنسيق مناسب
    public function getEventTimeFormatted()
    {
        if (!empty($this->event_time)) {
            try {
                return Carbon::parse($this->event_time)->format('H:i');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
