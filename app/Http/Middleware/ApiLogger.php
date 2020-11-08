<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Helper;
use Log;
class ApiLogger
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

        

        $response=$next($request);
        
        Log::channel('api_logger')->info(['URL'=>$request->url(),'Request'=>$request->all(),'Response'=>isset($response->original)?$response->original:$response]);
            
        return $response;
    }
}