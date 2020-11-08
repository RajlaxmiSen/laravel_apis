<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Registration;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Helper;
use Carbon\Carbon;
use JWTAuthException;
use Config;
use DB;
use Illuminate\Support\Facades\Auth;

class ForgotPassword_Controller extends Controller
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

    public function forgotPassword(Request $request){
        if (isset($request->email)) {
            $user = User::where('email', $request->email)->first();
            if(isset($user) && !empty($user)){
                $otp = mt_rand(100000, 999999);
                $expiry = Carbon::now()->addMinutes(Config::get('custom_setting.OTP_VALIDITY_MINUTE'));
                DB::table('password_resets')->insert(['email' => $request->email, 'otp' => $otp , 'otp_expiry' => $expiry, 'created_at' => now()]);
                $result = Helper::sendForgotOTPEmail($request->email ,$otp ,$expiry);
                    if($result){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Opt send to your email please verify. ";
                    }else{
                        $this->response['response'] = 1;
                        $this->response['success'] = 0;
                        $this->response['message'] = "Not able to send mail";  
                    }
            }else{
                $this->response['message'] = "User not found";
            }   
        } else {
            $this->response['message'] = "Please fill email";
        }
        return $this->response;
       
   }

    public function resetPassword(Request $request){
        if(isset($request->email) && isset($request->otp) && isset($request->password)){
            $password_reset = DB::table('password_resets')->where('otp',$request->otp)->first();
            if(isset($password_reset) && !empty($password_reset)){
                $otp_time = Carbon::parse($password_reset->otp_expiry);
                if($otp_time >= Carbon::now()){
                    $user = User::where('email', $password_reset->email)->first();
                    if(isset($user)){
                        $user->password = Hash::make($request->password);
                        if($user->save()){
                            DB::table('password_resets')->where('email', $user->email)->delete();
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message'] = "Password reset successfully";
                        }else{
                            $this->response['message'] = "Something went wrong";
                        }
                    }else{
                        $this->response['message'] = "Email not found";
                    }
                }else{
                    $this->response['message'] = "Your opt is exprie"; 
                }
            }else{
                $this->response['message'] = "Invalid Otp";
            }
        }else{
            $this->response['message'] = "Data missing";
        }
        return $this->response;
    }


}
