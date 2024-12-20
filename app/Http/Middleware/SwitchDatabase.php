<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class SwitchDatabase
{
    public function handle($request, Closure $next)
    {
        $database = $request->route('database');
        $validDatabases = ['jo', 'sa', 'eg', 'ps'];

        // التحقق من صحة اسم قاعدة البيانات
        if (!in_array($database, $validDatabases)) {
            $database = 'jo';
        }

        // تعيين اتصال قاعدة البيانات
        Config::set('database.default', $database);

        return $next($request);
    }
}
