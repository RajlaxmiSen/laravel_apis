<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\FriendConnection;
use JWTAuth;
use Config;
use Storage;
use NotificationHelper;

class Message_Controller extends Controller
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

    /**
    *@ request : post 
    *@ return : save message to database 
    */

    public function sendMessage(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $send_message = false ;

        if(isset($user)){
            if(isset($request->message,$request->to_user_id)){
                $to_user = User::where('id', $request->to_user_id)->first();
                if(isset($to_user) && !empty($to_user)){
                    $friends = FriendConnection::where('status', 1)->where([
                        ['requester_id','=', $to_user->id],
                        ['requestee_id','=', $user->id]
                    ])->Orwhere([
                        ['requestee_id','=', $to_user->id],
                        ['requester_id','=', $user->id]
                    ])->get();
                    if($friends->count()){
                        $message = Message::create(['from_user'=>$user->id ,'to_user'=>$request->to_user_id, 'message'=>$request->message]);
                        $token = JWTAuth::getToken();
                        $type = 1;
                        $title = "New message received ";
                        //$message = "Hello";
                        $test = NotificationHelper::send($token, $type, $title, $message);
                        //dd($test);
                        if($message){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message_id'] = $message->id;   
                            $this->response['message'] = $request->message;            
                        }else{
                            $this->response['message'] = "Something went wrong";        
                        }
                    }else{
                        $this->response['message'] = "This user is not your friend, please send a friend request in order to send messages";
                    }
                    
                }else{
                    $this->response['message'] = "No user found";   
                }
            }else{
                $this->response['message'] = "Please select user or type message to send";  
            }
        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : single user messages 
    */

    public function getUserMessages(Request $request){
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 20;
        $offset = ($page-1) * $total_records_per_page; 

        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user)){
            if(isset($request->to_user_id)){
                $send_user = User::where('id', $request->to_user_id)->first();
                if(isset($send_user) && !empty($send_user) && $send_user->count()!=0){
                    $messages = Message::where([
                        ['to_user','=', $send_user->id],
                        ['from_user','=', $user->id]
                    ])->Orwhere([
                        ['from_user','=', $send_user->id],
                        ['to_user','=', $user->id]
                    ])->orderBy('created_at','desc');
                    if(isset($messages) && !empty($messages)){
                        $messages = $messages->offset($offset)->limit($total_records_per_page)->get();
                        if($messages->count() != 0){
                            $message_array = [];
                            foreach ($messages as $key => $message) {
                                // $name = $message->toUser->first_name." ".$message->toUser->last_name;
                                // $image = $message->toUserProfile->profile_image;
                                // $data['user']['id'] = $message->toUser->id;
                                // $data['user']['name'] = $name;
                                // $data['user']['profile_image'] = asset('/public/storage/profile_images/'.$image);
                                $type = ($message->from_user == $user->id) ? 'S': 'R';
                                array_push($message_array,[
                                    'message_id' => $message->id,
                                    'message' => $message->message,
                                    'type' => $type ,
                                    'date' => date("Y-m-d H:i:s" , strtotime($message->created_at)),
                                ]);
                            }
                            //$data['message'] = 
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['data'] = $message_array;;
                            $this->response['message'] = "Data found";
                        }else{
                            $this->response['message'] = "No message found";
                        }
                    }else{
                        $this->response['message'] = "No message found";    
                    }
                }else{
                    $this->response['message'] = "Send user not found";
                }
            }else{
                $this->response['message'] = "Data missing ";   
            }
        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : all friend user messages 
    */

    public function getAllMessages(Request $request){
        $user_ids = [] ;
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $friends_data = [];
        $data = [];

        if(isset($user)){

            $friends_data = FriendConnection::where('status', 1)->where(function($q)use($user){
                $q->where('requester_id', $user->id);
                $q->Orwhere('requestee_id',$user->id);
            })->get();

            if($friends_data->count() !=0 ){

                foreach ($friends_data as $key => $friend) {
                    $user_ids[$friend->requester_id] = $friend->id;
                    $user_ids[$friend->requestee_id] = $friend->id;
                } 
                unset($user_ids[$user->id]);
                //dd($user_ids);
                $data_array = [];
                foreach ($user_ids as $user_id => $friendConnection_id) {
                    //dd($id);
                    $sendUser = User::where('id', $user_id)->first();
                    $message = Message::where([
                        ['to_user','=', $user_id],
                        ['from_user','=', $user->id]
                    ])->Orwhere([
                        ['from_user','=', $user_id],
                        ['to_user','=', $user->id]
                    ])->orderBy('created_at','desc')->first();
                    //dd($message);
                    if(isset($message) && !empty($message)){
                        $image = $sendUser->profile->profile_image;
                        $data_array['user_id'] = $sendUser->id;
                        $data_array['user_name'] = $sendUser->first_name." ".$sendUser->last_name;
                        $data_array['profile_image'] = asset('/public/storage/profile_images/'.$image);
                        $data_array['message_id'] = $message->id;
                        $data_array['message'] = $message->message;
                        $data_array['type'] = ($message->from_user == $user->id) ? 'S': 'R';
                        $data_array['date'] = date("Y-m-d H:i:s" , strtotime($message->created_at));
                        array_push($data, $data_array);
                    }
                }
                $this->response['response'] = 1;
                $this->response['success'] = 1;
                $this->response['data'] = $data;
                $this->response['message'] = "Data found";
            }else{
                $this->response['message'] = "User not found";
            }
        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    public function getFriendsName(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $loggedInUser =JWTAuth::toUser(JWTAuth::getToken()); 
        $data = [];
        $ids_data = [];
        if(isset($loggedInUser) && !empty($loggedInUser)){
            $friends_data = FriendConnection::where('status', 1)->where(function($q)use($loggedInUser){
                $q->where('requester_id', $loggedInUser->id);
                $q->Orwhere('requestee_id',$loggedInUser->id);
            })->get();

            $check_data = [];
            if(isset($friends_data) && !empty($friends_data)){
                foreach ($friends_data as $key => $friend) {
                   $ids_data[$friend->requester_id] = $friend->id;
                   $ids_data[$friend->requestee_id] = $friend->id;
                }
                unset($ids_data[$loggedInUser->id]);

                $users = User::whereIn('id',array_keys($ids_data));

                if(isset($request->first_name) && strlen($request->first_name)>=3){
                    $users = $users->where('first_name','like', "%".$request->first_name."%");
                }

                if(isset($request->last_name) && strlen($request->last_name)>=3){
                   $users = $users->where('last_name','like', "%".$request->last_name."%");
                }

                $users = $users->with('profile')->get();

                $data_array = [];
                foreach ($users as $key => $user) {

                    $message = Message::where([
                        ['to_user','=', $user->id],
                        ['from_user','=', $loggedInUser->id]
                    ])->Orwhere([
                        ['from_user','=', $user->id],
                        ['to_user','=', $loggedInUser->id]
                    ])->orderBy('created_at','desc')->first();
                    //dd($message);
                    if(isset($message) && !empty($message)){
                        $image = $sendUser->profile->profile_image;
                        $data_array['user_id'] = $sendUser->id;
                        $data_array['user_name'] = $sendUser->first_name." ".$sendUser->last_name;
                        $data_array['profile_image'] = asset('/public/storage/profile_images/'.$image);
                        $data_array['message_id'] = $message->id;
                        $data_array['message'] = $message->message;
                        $data_array['type'] = ($message->from_user == $user->id) ? 'S': 'R';
                        $data_array['date'] = date("Y-m-d H:i:s" , strtotime($message->created_at));
                        array_push($data, $data_array);
                    }
                }
                
            }
        }else{
            $this->response['message'] = "User not found";
        }
    }
}
