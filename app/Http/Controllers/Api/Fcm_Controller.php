<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\User;
use Helper;
use JWTAuthException;
use Config;
use JWTAuth;

class Fcm_Controller extends Controller
{
    public $response;
    public function __construct()
    {
        $this->response = [
            'response' => 1,
            'success' => 0,
            'message' => 'Invalid Request',
        ];

        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function fcmToken(Request $request){

    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
        	if(isset($request->new_fcm_token, $request->imei) && !empty($request->new_fcm_token) && !empty($request->imei)){
        		$fcmToken = FcmToken::where('imei', $request->imei)->where('user_id', $user->id)->first();
                //dd($fcmToken);
        		if(!isset($fcmToken) && empty($fcmToken)){
        			$saveToken = FcmToken::create(['user_id'=> $user->id, 'fcm_token'=> $request->new_fcm_token, 'imei'=> $request->imei]);
        			if($saveToken->id){
        				$this->response['response'] = 1;
	            		$this->response['success'] = 1;
	            		$this->response['message'] = "Fcm token added ";
        			}
        		}else{
        			$fcmToken->delete();
        			$saveNewToken = FcmToken::create(['user_id'=> $user->id, 'fcm_token'=> $request->new_fcm_token, 'imei'=> $request->imei]);
        			if($saveNewToken->id){
        				$this->response['response'] = 1;
	            		$this->response['success'] = 1;
	            		$this->response['message'] = "New Fcm token added ";
        			}
        		}

        	}else{
        		$this->response['message'] = "Data missing" ;
        	}
        }else{
        	$this->response['message'] = "User not found ";
        }

    	return response()->json($this->response);
    }
}
