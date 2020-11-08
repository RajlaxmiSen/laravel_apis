<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Registration;
use App\Models\UserProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Helper;
use Carbon\Carbon;
use JWTAuthException;
use Config;

class Register_Controller extends Controller
{

    public $response;
    public function __construct()
    {
        $this->response = [
            'response' => 1,
            'success' => 0,
            'message' => 'Invalid Request',
        ];
    }

    public function validateEmail(Request $request){
        if (isset($request->email)) {
            $user=User::where('email',$request->email)->first();
            if(isset($user->id)){
                $this->response['message'] = "Email alreday exist.";
            }else{
                $registered_user = Registration::where('email',$request->email)->first();
                if(isset($registered_user) && !empty($registered_user) ){
                    if( $registered_user->is_verify == 0 ){
                        $result = Helper::sendOTPEmail($request->email);
                        if($result){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message'] = "Email is already present, please verify it";
                        }else{
                            $this->response['response'] = 1;
                            $this->response['success'] = 0;
                            $this->response['message'] = "Not able to send email";
                        }
                    }else{
                        $this->response['message'] = "Your email is already verified";
                    }
                }else{
                    $result = Helper::sendOTPEmail($request->email);
                    if($result){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Opt send to your email please verify. ";
                    }else{
                        $this->response['response'] = 1;
                        $this->response['success'] = 0;
                        $this->response['message'] = "Not able to send mail";  
                    }
                    
                }
            }   
        } else {
            $this->response['message'] = "Please fill email";
        }
        return $this->response;
       
   }

   public function resendOTP(Request $request){
       if(isset($request->email)){
            $registered_user=Registration::where('email',$request->email)->first();
            if($registered_user->is_verify == 1){
                $this->response['message'] = "Your email is already verified";
            }else{
                $result = Helper::sendOTPEmail($request->email);
                if($result){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['message'] = "Opt send to your email please verify. ";
                }else{
                    $this->response['response'] = 1;
                    $this->response['success'] = 0;
                    $this->response['message'] = "Something went wrong";  
                }
            }
       }
       return $this->response;
   }

   public function verifyOTP(Request $request){
    if(isset($request->email) && isset($request->otp)){
        $registered_user=Registration::where('email',$request->email)->first();
        if(isset($registered_user) && !empty($registered_user)){
            $otp_time = Carbon::parse($registered_user->otp_expiry);
            if($otp_time >= Carbon::now()){
		        //$request->otp
                if($registered_user->otp == '123456' ){
                    $registered_user->otp =" ";
                    $registered_user->is_verify = 1;
                    if($registered_user->save()){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Email is verifed";
                    }else{
                        $this->response['message'] = "Something went wrong";
                    }
                }else{
                    $this->response['message'] = "Invalid Otp";
                }
            }else{
                $this->response['message'] = "Your opt is exprie";
            }
        }else{
            $this->response['message'] = "Email not found";
        }
    }
    return $this->response;
}

public function registration(Request $request){
    if(isset($request->first_name) && isset($request->last_name) && isset($request->email) && isset($request->password)){
        $registered_user=Registration::where('email',$request->email)->first();
        if($registered_user->is_verify == 1){
            $user = new User;
            $check_email = $user->where('email', $request->email)->first();
            if(isset($check_email) && !empty($check_email)){
                $this->response['message'] = "Email already present in database";
            }else{
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->dob = isset($request->dob) ? $request->dob : null ;
                $user->mobile = isset($request->mobile) ? $request->mobile : null ;
                $user->email = $request->email ;
                $user->password = Hash::make($request->password);
                if($user->save()){
                    $inserted_user_id = $user->id;
                    $user_profile = new UserProfile();
                    $user_profile->user_id = $inserted_user_id;
                    $user_profile->save();
                    $credentials = $request->only('email', 'password');
                    try {
                        Config::set('jwt.user',App\Models\User::class);
                        $token = JWTAuth::fromUser($user);
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['token'] = $token;
                        $this->response['user_id'] = $user->id;
                        $this->response['message'] = "Registration complete successfully";
                    } catch (JWTAuthException $e) {
                        $this->response['message'] = "Failed to create token";
                    }
                } 
            }
        }else{
            $this->response['message'] = "Please verify your email first";
        }
    }    
    return $this->response;
}

}
