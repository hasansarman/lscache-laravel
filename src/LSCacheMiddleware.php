<?php

namespace Litespeed\LSCache;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LSCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string                   $lscache_control
     * @return mixed
     */
    public function handle($request, Closure $next, string $lscache_control = null)
    {
        $response = $next($request);

        if (!in_array($request->getMethod(), ['GET', 'HEAD']) || !$response->getContent()) {
            return $response;
        }

        $esi_enabled    = config('lscache.esi');
        $maxage         = config('lscache.default_ttl', 0);
        $cacheability   = config('lscache.default_cacheability');
        $guest_only     = config('lscache.guest_only', false);

        $suffix_key_format = config('lscache.suffix_key_format');
        $enabled = config('lscache.enabled');
        $excluded_pages=config('lscache.exclude_pages');

        $route = $request->route();

        if ($route && in_array($route->getName(), $excluded_pages)) {
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

            return $response;
        }

        $user_id=0;
        try {
            if ($user = Sentinel::check())
            {
                // User is logged in and assigned to the `$user` variable.
                $user_id=$user->id;
            }


        } catch (Exception $e) {

        }





        if($enabled==false){
            return $response;
        }
        if ($maxage === 0 && $lscache_control === null) {
            return $response;
        }

        if ($guest_only && $user_id!=0) {
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

            return $response;
        }

        $lscache_string = "max-age=$maxage,$cacheability";

        if (isset($lscache_control)) {
            $lscache_string = str_replace(';', ',', $lscache_control);
        }

        if (Str::contains($lscache_string, 'esi=on') == false) {
            $lscache_string = $lscache_string.($esi_enabled ? ',esi=on' : null);
        }

        if ($response->headers->has('X-LiteSpeed-Cache-Control') == false) {
            $response->headers->set('X-LiteSpeed-Cache-Control', $lscache_string);
        }

        return $response;
    }
}
