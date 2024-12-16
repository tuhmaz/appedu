<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLogin extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'attempted_at'
    ];

    protected $casts = [
        'attempted_at' => 'datetime'
    ];
}
