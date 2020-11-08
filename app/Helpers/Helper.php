<?php

namespace App\Helpers;

use Carbon\Carbon;
use Config;
use App\Models\User;
use App\Models\Registration;
use Mail;
use App\Mail\SendOtptoEmail;
use App\Mail\SendPassword;
use App\Mail\SendForgotOTPEmail;
use App\Mail\SendAdminForgotOtpEmail;
use DB;
use Log;
use Auth;
use URL;

class Helper
{

    public static function sendOTPEmail($email)
    {
        if (isset($email)) {
            try {
                $registered_user = Registration::where('email',$email)->first();
                $otp = mt_rand(100000, 999999);
                $otp = '123456';
                $expiry = Carbon::now()->addMinutes(Config::get('custom_setting.OTP_VALIDITY_MINUTE'));
                $data_saved = false;
                if(isset($registered_user) && !empty($registered_user)){
                    $registered_user->otp = $otp;
                    $registered_user->otp_expiry = $expiry;
                    if($registered_user->save()){
                        $data_saved = true;
                    }
                }else{
                    $register = new Registration;
                    $register->email  = $email;
                    $register->otp    = $otp;
                    $register->otp_expiry = $expiry;
                    if($register->save()){
                        $data_saved = true;
                    }
                }
                return true;
                // if ($data_saved) {
                //     Mail::to($email)->send(new SendOtptoEmail(['otp' => $otp, 'otp_expiry' => $expiry->format('d M Y, h:i A')]));
                //     if (!Mail::failures()) {
                //         return true;
                //     } else {
                //         return false;
                //     }
                // } else {
                //     return false;
                // }
            } catch (\Exception $e) {
                dd($e->getMessage());
                return false;
            }
        }
        return false;
    }

    public static function sendPassword($name, $email, $password){
        if(isset($email,$password)){
            try {
                Mail::to($email)->send(new SendPassword(['name'=>$name, 'password' => $password]));
                    if (!Mail::failures()) {
                        return true;
                    } else {
                        return false;
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return false;
                }
        }
        return false;
    }

    public static $friend_request_status = [0 => 'Pending', 1 => 'Accepted', 2 => 'Rejected', 3 => 'Unfriend', 4 => 'Block', 5 => 'Cancel'];

    public static function sendForgotOTPEmail($email, $otp ,$expiry){
        if(isset($email, $otp, $expiry)){
            try {
                Mail::to($email)->send(new SendForgotOTPEmail(['otp' => $otp ,'otp_expiry' => $expiry ]));
                if (!Mail::failures()) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
        return false;
    }

    public static function sendAdminForgotOTPEmail($email, $otp ,$expiry){
        if(isset($email, $otp, $expiry)){
            try {
                Mail::to($email)->send(new SendAdminForgotOtpEmail(['otp' => $otp ,'otp_expiry' => $expiry ]));
                if (!Mail::failures()) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
        return false;
    }

    public static function convertSearchDataDecode($sr_dt = ""){
        if (strlen($sr_dt) > 0) {
            $decoded_string = base64_decode($sr_dt);

            parse_str($decoded_string, $result);

            return $result;
        } else {
            return [];
        }
    }

    public static function convertSearchDataEncode($sr_dt = []){
        if (count($sr_dt) > 0 && is_array($sr_dt)) {

            $string = "";
            foreach ($sr_dt as $k => $v) {
                if (is_string($k) && (is_string($v) || $v == "")) {

                    $string .= $k . '=' . $v . '&';
                } else {
                    return "";
                }
            }
            return base64_encode(rtrim($string, '&'));
        } else {
            return "";
        }
    }

    public static $admin_status = [0 => 'none', 1 => 'Approved', 2 => 'Disapproved'];

}

