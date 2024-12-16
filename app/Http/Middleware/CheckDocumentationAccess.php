<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDocumentationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // تحقق من أن المستخدم لديه صلاحية الوصول للتوثيق
        if (!Auth::user()->hasPermissionTo('view-api-documentation')) {
            abort(403, 'Unauthorized access to API documentation.');
        }

        return $next($request);
    }
}
