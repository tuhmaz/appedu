<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Config;

class SwitchDatabase
{

  public function handle($request, Closure $next)
    {

        $selectedCountry = $request->input('country');

        switch ($selectedCountry) {
            case 'sa':
                Config::set('database.default', 'subdomain1');
                break;
            case 'eg':
                Config::set('database.default', 'subdomain2');
                break;
            case 'ps':
                Config::set('database.default', 'subdomain3');
                break;
            default:
                Config::set('database.default', 'mysql');
                break;
        }


        return $next($request);
    }
}
