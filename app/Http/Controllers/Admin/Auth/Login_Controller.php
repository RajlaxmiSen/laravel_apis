<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin;
use App\Models\FcmToken;
use Config;
use Helper;
use Auth;

class Login_Controller extends Controller
{   
    use AuthenticatesUsers;
    protected $redirectTo = 'admin/';
   	public function __construct(){
       	$this->middleware('admin_guest')->except('logout');
    }

   	public function showLoginForm(){
     	return view('admin.auth.login');
 	}

 	public function adminLogin(Request $request){
    	
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended('admin/');
        }
        return back()->withInput($request->only('email'))->withError('Invalid login or password ');
    }

    public function logout(Request $request){
        return redirect('admin/login')->with(Auth::guard('admin')->logout());
    }

}
