<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;

class UpdateUserStatus
{
    public function handleLogin(Login $event)
    {
        $user = $event->user;
        $user->status = 'online';
        $user->save();
    }

    public function handleLogout(Logout $event)
    {
        $user = $event->user;
        if ($user) {
            $user->status = 'offline';
            $user->last_seen_at = now();
            $user->save();
        }
    }
}
