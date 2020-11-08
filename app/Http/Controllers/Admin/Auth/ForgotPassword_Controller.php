<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Carbon\Carbon;
use Helper;
use Config;
use DB;
use Session;

class ForgotPassword_Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_guest')->except('logout');
    }

    public function showLoginForm(){
     	return view('admin.auth.reset_password_email');
 	}

    protected function sendMail(Request $request)
    {	
    	
    	if (isset($request->email)) {
            $user = Admin::where('email', $request->email)->first();
            if(isset($user) && !empty($user)){
                $otp = mt_rand(100000, 999999);
                $expiry = Carbon::now()->addMinutes(Config::get('custom_setting.OTP_VALIDITY_MINUTE'));
                $email = $request->email;
                DB::table('admin_password_resets')->insert(['email' => $request->email, 'otp' => $otp , 'otp_expiry' => $expiry, 'created_at' => now()]);
                $result = Helper::sendAdminForgotOTPEmail($request->email ,$otp ,$expiry);
                    if($result){
                       return redirect()->to('admin/password/reset')->with('email', $email);
                    }else{
                    	Session::flash('error', 'Not able to send mail'); 
                        back();
                    }
            }else{
            	Session::flash('error', 'User not found'); 
                back();
            }   
        } else {
        	Session::flash('error', 'Please fill email'); 
            back();
        }
        return back()->withInput($request->only('email'));
    }
}
