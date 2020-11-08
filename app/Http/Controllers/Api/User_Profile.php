<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\CanHelpWith;
use App\Models\NeedHelpWith;
use App\Models\Category;
use App\Models\State;
use App\Models\Country;
use JWTAuth;
use Config;
use Storage;

class User_Profile extends Controller
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

    public function getProfile(){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $canHelpWith = CanHelpWith::where('user_id',$user->id)->pluck('category_id')->toArray();
        $needHelpWith = NeedHelpWith::where('user_id',$user->id)->pluck('category_id')->toArray();  
        if(isset($user)){
            $this->response['response'] = 1;
            $this->response['success'] = 1;
            $this->response['message'] = "Profile fetched!!";
            $this->response['data']['first_name'] = $user->first_name;
            $this->response['data']['last_name'] = $user->last_name;
            $this->response['data']['email'] = $user->email;
            $this->response['data']['mobile'] = $user->mobile;
            $this->response['data']['mobile_visibility'] = $user->profile->mobile_visibility;
            $this->response['data']['email_visibility'] = $user->profile->email_visibility;
            $this->response['data']['dob'] = isset($user->dob) ? date('d-m-Y', strtotime($user->dob)) : null;
            $image = $user->profile->profile_image; 
            $this->response['data']['profile_image'] = asset('/public/storage/profile_images/'.$image);
            $this->response['data']['about_info'] = isset($user->profile->about_info) ? $user->profile->about_info :"";
            $this->response['data']['posts_count'] = $user->profile->posts_count;
            $this->response['data']['friends_count'] = $user->profile->friends_count;
            $this->response['data']['can_help_with'] = implode(",", $canHelpWith);
            $this->response['data']['need_help_with'] = implode(",", $needHelpWith);
        }else{
            $this->response['message'] = "Profile not found";
        }
        return response()->json($this->response);
    }

    public function updateProfile(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $msg = "";
        $data_need = [];
        $data_can = [];
        if(isset($user)){
            if(isset($request->first_name) && strlen($request->first_name)>=4){
                $user->first_name = $request->first_name;
            }
            if(isset($request->last_name)){
                $user->last_name = $request->last_name;
            }
            // if(isset($request->email) && strlen($request->email)!=0){
            //     if (filter_var($request->email, FILTER_VALIDATE_EMAIL) && ) {
            //         $check_email = User::find('email',$request->email);
            //         if(isset($check_email)){
            //             $msg = "Email is already present in database.";
            //         }else{
            //             $user->email = $request->email;
            //         }   
            //     }else{
            //         $msg.= "Not a vaild email.";
            //     }
            // }

            if(isset($request->mobile) && strlen($request->mobile)!= 0){
                $user->mobile = $request->mobile;
            }
            if(isset($request->dob) && strlen($request->dob)!=0){
                $user->dob = date('Y-m-d', strtotime($request->dob));
            }
            
            $user_profile = UserProfile::where('user_id',$user->id)->first();

            if(isset($request->mobile_visibility) && strlen($request->mobile_visibility)!= 0){
                $user_profile->mobile_visibility = $request->mobile_visibility;
            }
            
            if(isset($request->email_visibility) && strlen($request->email_visibility)!= 0){
                $user_profile->email_visibility = $request->email_visibility;
            }

            if(isset($request->about_info) && strlen($request->about_info)!= 0){
                $user_profile->about_info = $request->about_info;
            }

            if(isset($request->can_help_with) && strlen($request->can_help_with)!= 0){
                $can_help_withs = explode(",", $request->can_help_with);
                CanHelpWith::where('user_id', $user->id)->delete();
                foreach($can_help_withs as $key =>$value){
                   $data_need[$key] = CanHelpWith::create(['user_id' => $user->id, 'category_id' =>$value]);
                }
            }

            if(isset($request->need_help_with) && strlen($request->need_help_with)!= 0){
                $need_help_withs = explode(",", $request->need_help_with);
                NeedHelpWith::where('user_id', $user->id)->delete();
                foreach($need_help_withs as $key =>$value){
                    $data_can[$key] = NeedHelpWith::create(['user_id' => $user->id, 'category_id' =>$value]);
                } 
            }
            
            if(isset($request->state) && strlen($request->state)!= 0){
                $check_state = State::where('name', $request->state)->first();
                if($check_state !==null){
                    $user_profile->state = $check_state->name;
                    $user_profile->state_id = $check_state->id;
                }else{
                    $state = State::create(['name', $request->state]);
                    if($state->id){
                        $user_profile->state = $request->state;
                        $user_profile->state_id = $state->id;
                    }
                } 
            }

            if(isset($request->contry) && strlen($request->contry)!= 0){
                $check_country = Country::where('name', $request->contry)->first();
                if($check_country !== null){
                    $user_profile->country = $check_country->name;
                    $user_profile->country_id = $check_country->id;
                }else{
                    $country = Country::create(['name', $request->country]);
                    if($country->id){
                        $user_profile->country = $request->state;
                        $user_profile->country_id = $country->id;
                    }
                } 
            }

            if( ($user_profile->save() || $user->save()) && (count($data_can) !=0  || count($data_need) !=0 ) ){

                $this->response['response'] = 1;
                $this->response['success'] = 1;
                $this->response['message'] = "Profile updated";
            }else{
                $this->response['message'] = $msg;  
            }

        }else{
            $this->response['message'] = "Profile not found";
        }
        return response()->json($this->response);
    }


    public function uploadProfileImage(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $user_profile = UserProfile::where('user_id',$user->id)->first();
        if ($request->hasFile('profile_image')) {
            $imageName = time().'.'.request()->profile_image->getClientOriginalExtension();
            request()->profile_image->move(storage_path('/app/public/profile_images/'), $imageName);
            $user_profile->profile_image = $imageName;
            if($user_profile->save()){
                $this->response['response'] = 1;
                $this->response['success'] = 1;
                $this->response['message'] = "Image uploaded";
                $this->response['profile_image'] = asset('/public/storage/profile_images/'.$imageName);
            }else{
                $this->response['message'] = "Error while uploading";
            }
            
        }else{
            $this->response['message'] = "Please select image";
        }

        return response()->json($this->response);
    }

    public function updateSetting(){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $user_profile = UserProfile::where('user_id',$user->id)->first();
        if(isset($user)){
            if(isset($request->mobile_visibility) && strlen($request->mobile_visibility)!= 0){
                $user_profile->mobile_visibility = $request->mobile_visibility;
            }
            
            if(isset($request->email_visibility) && strlen($request->email_visibility)!= 0){
                $user_profile->email_visibility = $request->email_visibility;
            }

            if($user_profile->save()){
                $this->response['response'] = 1;
                $this->response['success'] = 1;
                $this->response['message'] = "Setting updated ";
            }else{
                $this->response['message'] = "Something went wrong";
            }

        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    // public function downloadProfileImage(Request $request){
    //     Config::set('jwt.user', App\Models\User::class);
    //     $user =JWTAuth::toUser(JWTAuth::getToken());
    //     $image = $user->profile->profile_image; 
    //     return Storage::download($image);
    // }
}
