<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\User as User;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Hash;
use Helper;
use JWTAuthException;
use Config;
use App\Models\UserProfile;
use Auth;

class Login_Controller extends Controller
{
    /*login_type @type int
        0 -> general login
        1 -> facebook
        2 -> google
     */  
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function testAuth(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user = User::where('email' , $request->email)->first();
        $token = Auth::login($user);

        return $token;
    }

    public function testProfile(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        dd($user);
    }


    public function login(Request $request)
    {
        $response['response'] = 1;
        $response['success'] = 0;
        $response['message'] = "Invalid Request";
        $credentials = $request->only('email', 'password');
        $user_model = new User;
        $token = null;
        $sociallogin = isset($request->login_type) ? $request->login_type : 0;
        if ($sociallogin == 1 || $sociallogin == 2)
        {
            $check_user = $user_model->where('email', $request->email)->first();
            if (isset($check_user) && !empty($check_user))
            {   
                try{
                    Config::set('jwt.user', App\Models\User::class);
                    //$token = JWTAuth::fromUser($user_model);
                    $token = Auth::login($check_user);
                    if($token){
                        $response['response'] = 1;
                        $response['success'] = 1;
                        $response['token'] = $token;
                        $response['user_id'] = $check_user->id;
                        $response['message'] = "Login Successfully";
                    }else{
                        $response['message'] = "Something went wrong";
                    } 
                }catch(JWTAuthException $e){
                    $response['message'] = "Failed to create token";
                }  
            }
            else
            {
                if (isset($request->first_name, $request->last_name, $request->email))
                {
                    $password = mt_rand(10000000, 99999999);
                    $mail_status = Helper::sendPassword($request->first_name , $request->email, $password);
                    if($mail_status){
                        $user_model->first_name = $request->first_name;
                        $user_model->last_name = $request->last_name;
                        $user_model->dob = isset($request->dob) ? $request->dob : null;
                        $user_model->mobile = isset($request->mobile) ? $request->mobile : null;
                        $user_model->email = $request->email;
                        $user_model->login_type = $request->login_type;
                        $user_model->password = Hash::make($password);
                        if ($user_model->save())
                        {   
                            $inserted_user_id = $user_model->id;
                            $user_profile = new UserProfile();
                            $user_profile->user_id = $inserted_user_id;
                            $user_profile->save();
                            try
                            {
                                Config::set('jwt.user', App\Models\User::class);
                                //$token = JWTAuth::fromUser($user_model);
                                $token = Auth::login($user_model);
                                if($token){
                                    $response['response'] = 1;
                                    $response['success'] = 1;
                                    $response['token'] = $token;
                                    $response['user_id'] = $user_model->id;
                                    $response['message'] = "Login Successfully";
                                }else{
                                    $response['message'] = "Something went wrong";
                                }
                            }
                            catch(JWTAuthException $e)
                            {
                                $response['message'] = "Failed to create token";
                            }
                        }
                    }else{
                        $response['message'] = "Unable to send mail";
                    }
                }
                else
                {
                    $response['message'] = "Data Missing!";
                }
            }

        }
        else
        {
            if (isset($request->email, $request->password))
            {
                try
                {
                    Config::set('jwt.user', User::class);
                    if (!$token = JWTAuth::attempt($credentials))
                    {
                        $response['message'] = "Invalid Email or Password";
                        return response()->json($response);
                    }
                }
                catch(JWTAuthException $e)
                {
                    $response['message'] = "Failed to create token";
                }

                $user = auth()->user() ;
                $response['token'] = $token;
                $response['user_id'] = $user->id;
                $response['success'] = 1;
                $response['message'] = "Login Successfully";
            }
            else
            {
                $response['message'] = "Data Missing!";
            }

        }
        return response()->json($response);
    }

    public function logout(Request $request)
    {
        $response['response'] = 1;
        $response['success'] = 0;
        $response['message'] = "Invalid Request";
        if(auth()->logout()){
            $response['response'] = 1;
            $response['success'] = 1;
            $response['message'] = "Successfully logged out";
        }
        return response()->json($response);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

}

