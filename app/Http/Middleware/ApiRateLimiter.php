<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiRateLimiter
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

        $limit = 3;
        $route_name = $request->route()->getName();
        $uuid = $request->uuid;

        // $blocked_forever = Cache::get($uuid . $route_name . '-blocked-forever');
        // if ($blocked_forever) {
        //     return $this->return_error(
        //         'Too Many Attempts',
        //         111,
        //         'Your device has been permanently blocked. Please contact Hibou if you wish to unblock your device.'
        //     );
        // }

        $blocked = Cache::get($uuid . $route_name . '-blocked', 0);
        if ($blocked) {
            $blocked_expiry = Cache::get($uuid . $route_name . '-blocked-expiry');
            return $this->return_error(
                'Too Many Attempts',
                111,
                'You have exceeded your maximum attempts. Pease try again in ' .
                    Carbon::createFromTimestamp($blocked_expiry)->diffInHours(Carbon::now()) .
                    ' hour(s) and ' .
                    Carbon::createFromTimestamp($blocked_expiry)->diffInMinutes(Carbon::now()) % 60 .
                    ' minute(s).'
            );
        }
        $cached_attempts = Cache::get($uuid . $route_name . '-attempts');
        if ($cached_attempts) {
            if ($cached_attempts >= intval($limit)) {
                // $block_count = Cache::get($uuid . $route_name . '-block-count', 0);
                // if ($block_count >= 4) {
                //     Cache::forever($uuid . $route_name . '-blocked-forever', 1);
                //     $controller = new Controller;
                //     return $controller->return_error(
                //         'Too Many Attempts',
                //         111,
                //         'Your device has been permanently blocked. Please contact Hibou if you wish to unblock your device.'
                //     );
                // } else {
                $blocked_expiry = Carbon::now()->addHour(); // afer 1 hour expured
                Cache::forget($uuid . $route_name . '-attempts');
                Cache::forget($uuid . $route_name . '-attempts-expiry');
                Cache::put($uuid . $route_name . '-blocked', 1, $blocked_expiry);
                Cache::put($uuid . $route_name . '-blocked-expiry', $blocked_expiry->timestamp, $blocked_expiry);
                // $block_count_expiry = Carbon::now()->addDay();
                // Cache::forget($uuid . $route_name . '-block-count');
                // Cache::put($uuid . $route_name . '-block-count', $block_count + 1, $block_count_expiry);
                return $this->return_error(
                    'Too Many Attempts',
                    111,
                    'You have exceeded your maximum attempts. Pease try again in ' .
                        $blocked_expiry->diffInHours(Carbon::now()) .
                        ' hour(s) and ' .
                        $blocked_expiry->diffInMinutes(Carbon::now()) % 60 .
                        ' minute(s).'
                );
                // }
            } else {
                $attempts_expiry = Cache::get($uuid . $route_name . '-attempts-expiry');
                Cache::forget($uuid . $route_name . '-attempts');
                Cache::put($uuid . $route_name . '-attempts', $cached_attempts + 1, Carbon::createFromTimestamp($attempts_expiry)->diffInSeconds(Carbon::now()));
            }
        } else {
            // no attempts yet
            $expiresIn = Carbon::now()->addMinute();
            Cache::put($uuid . $route_name . '-attempts', 1, 1);
            Cache::put($uuid . $route_name . '-attempts-expiry', 1, 1);
        }
        return $next($request);
    }

    public function return_error($debugger = NULL, $code = 1, $message = NULL)
    {

        $error_data['error'] = array();
        $error_data['error']['message']     = $message;
        $error_data['error']['code']        = $code;
        $error_data['error']['debugger']    = $debugger;

        return response()->json($error_data)->setStatusCode(400);
    }
}
