<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CanHelpWith;
use App\Models\NeedHelpWith;
use JWTAuth;
use Config;

class Help_Controller extends Controller
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

    public function canHelpWith(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        if(isset($user) && !empty($user)){
            if(isset($request->category_ids) && strlen($request->category_ids) != 0){
                $category_ids = explode(",",$request->category_ids);
                $data = [];
                foreach($category_ids  as $key => $id ){
                    $data[$key] = CanHelpWith::create(['user_id' => $user->id, 'category_id' =>$id]);
                }
                if(count($data)){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['message'] = "Choice saved";
                }else{
                    $this->response['message'] = "Something went wrong, not able to save to database";
                }
            }else{
                $this->response['message'] = "Please select the category";
            }
        }else{
            $this->response['message'] = "User not found";
        } 
        
        return response()->json($this->response);
    }

    public function needHelpWith(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        if(isset($user) && !empty($user)){
            if(isset($request->category_ids) && strlen($request->category_ids) != 0){
                $category_ids = explode(",",$request->category_ids);
                $data = [];
                foreach($category_ids  as $key => $id ){
                    $data[$key] = NeedHelpWith::create(['user_id' => $user->id, 'category_id' =>$id]);
                }
                if(count($data)){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['message'] = "Choice saved";
                }else{
                    $this->response['message'] = "Something went wrong, not able to save to database";
                }
            }else{
                $this->response['message'] = "Please select the category";
            }
        }else{
            $this->response['message'] = "User not found";
        } 
        
        return response()->json($this->response);
    }
}
