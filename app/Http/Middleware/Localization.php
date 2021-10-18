<?php

namespace App\Http\Middleware;

use App;
use Config;
use Closure;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$locale = $request->header('accept-language');

		if($locale && in_array($locale, Config::get('app.locales')))
		{
			App::setLocale($locale);
		}


        return $next($request);
    }
}
