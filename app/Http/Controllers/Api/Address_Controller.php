<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Country;
use JWTAuth;
use Config;
use Storage;

class Address_Controller extends Controller
{
    public $response;
    public function __construct()
    {
        $this->response = [
            'response' => 1,
            'success'  => 0,
            'message'  => 'Invalid Request',
        ];

        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function addState(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user)){
	        if(isset($request->state) && strlen($request->state) != 0){
	            $check_state = State::where('name', $request->state)->first();
	            if($check_state == null ){
	                $state = State::create(['name', $request->state]);
	                if($state->id){
	                    $this->response['response'] = 1;
				        $this->response['success'] = 1;
				        $this->response['message'] = "City added";
	                }
	            }      
	        }else{
	        	$this->response['message'] = "Data missing";
	        }
	    }else{
	    	$this->response['message'] = "User not found";
	    }
        return response()->json($this->response);
    }

    public function addCountry(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        if(isset($user)){
	        if(isset($request->contry) && strlen($request->contry)!= 0){
	            $check_country = Country::where('name', $request->contry)->first();
	            if($check_country == null){
	                $country = Country::create(['name', $request->country]);
	                if($country->id){
	                   	$this->response['response'] = 1;
			            $this->response['success'] = 1;
			            $this->response['message'] = "State added";
	                }
	            } 
	        }else{
	        	$this->response['message'] = "Data missing";
	        }
    	}else{
    		$this->response['message'] = "User not found";
    	}
        return response()->json($this->response);
    }
}
