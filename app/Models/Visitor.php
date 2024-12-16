<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'ip_address',
        'session_id',
        'last_activity',
        'user_agent',
        'page_url',
        'country',
        'city',
        'device_type',
        'response_time',
    ];

    protected $dates = [
        'last_activity',
        'created_at',
        'updated_at'
    ];
}
