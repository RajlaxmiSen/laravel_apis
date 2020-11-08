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

class ResetPassword_Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_guest')->except('logout');
    }

 	public function showResetForm(Request $request, $token = null)
    {	
    	$email = Session::get('email');

    	//dd($request->all());
        return view('admin.auth.reset_password')->with(
            ['email' => $email]
        );
    }

    public function reset(Request $request){
    	
        if(isset($request->email) && isset($request->otp) && isset($request->password)){
            $password_reset = DB::table('admin_password_resets')->where('otp',$request->otp)->first();
            if(isset($password_reset) && !empty($password_reset)){
                $otp_time = Carbon::parse($password_reset->otp_expiry);
                if($otp_time >= Carbon::now()){
                    $user = Admin::where('email', $password_reset->email)->first();
                    if(isset($user)){
                        $user->password = Hash::make($request->password);
                        if($user->save()){
                            DB::table('password_resets')->where('email', $user->email)->delete();
                            Session::flash('status', 'Password reset'); 
            				return redirect()->to('admin/login');
                        }else{
                        	Session::flash('error', 'Something went wrong , Please try after sometime'); 
            				back();
                        }
                    }else{
                    	Session::flash('error', 'Email not found'); 
            			back();
                    }
                }else{
                	Session::flash('error', 'Your opt is exprie'); 
            		back();
                }
            }else{
            	Session::flash('error', 'Invalid Otp'); 
           		back();
            }
        }else{
        	Session::flash('error', 'Data missing'); 
            back();
        }
        return back()->withInput($request->only('email'));
    }
}
