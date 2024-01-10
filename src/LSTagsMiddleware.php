<?php

namespace Litespeed\LSCache;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Illuminate\Support\Facades\App;

class LSTagsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $lscache_tags
     * @return mixed
     */
    public function handle($request, Closure $next, string $lscache_tags = null)
    {
        $response = $next($request);

        $lscache_string = null;

        if (!in_array($request->getMethod(), ['GET', 'HEAD']) || !$response->getContent()) {
            return $response;
        }

        if(isset($lscache_tags)) {
            $lscache_string = str_replace(';', ',', $lscache_tags);


            $lscache_string.=$this->return_suffix();
        }

        if(empty($lscache_string)) {
            return $response;
        }

        if($response->headers->has('X-LiteSpeed-Tag') == false) {
            $response->headers->set('X-LiteSpeed-Tag', $lscache_string);
        }

        return $response;
    }

    private function return_suffix()
    {
        $suffix_key_format = config('lscache.suffix_key_format');
        $exploded=explode("+", $suffix_key_format);
        $returnstr="";
        foreach($exploded as $e){
            if($e =="LOCALE"){
                $returnstr.="_".App::getLocale();

            }
            else if($e=="USER"){
                if ($user = Sentinel::check())
                {
                    // User is logged in and assigned to the `$user` variable.
                    $returnstr.="_".$user->id;
                }

            }
        }
        return $returnstr;
    }
}
