<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;


class Register_Controller extends Controller
{
    use RegistersUsers;

    //protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('admin_guest');
    }

    public function showLoginForm(){
     	return view('admin.auth.register');
 	}

    protected function register(Request $request)
    {	
    	
    	$data = $this->validate($request, [
           'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
       
        if($admin->id){
        	
        	return redirect()->intended('admin/login');
        }
        return back()->withInput($request->only('email','name'));
    }

}
