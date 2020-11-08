<?php
namespace App\Http\Middleware;

use Auth;
use Closure;
use Helper;

class AuthenticateAdmin
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
        //dd( Auth::guard('admin')->user);
        if (false == Auth::guard('admin')->check()) {
            return redirect()->to("admin/login")->with('error', 'Unauthorized User');
        }
        return $next($request);

    }

}