<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Config;
use App\Models\User as User;
class VerifyUserJWTToken
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
        $response['response']=1;
        $response['success']=0;
        $response['msg']="Invalid Request";
   try{
            Config::set('jwt.user',User::class);
            $user = JWTAuth::toUser($request->header('user_token'));
            dd($user);
         
            if(!is_object($user)||get_class($user)!='App\Models\User'){
                $response['msg']="Token is invalid";
                return response()->json($response);
            }
            
        }
        catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $response['msg']="Token is expired";
                return response()->json($response);   
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                $response['msg']="Token is invalid";
                return response()->json($response);   
                
            }else{
                $response['msg']="Token is required";
                return response()->json($response);   
            }
        }

       return $next($request);
    }
}
