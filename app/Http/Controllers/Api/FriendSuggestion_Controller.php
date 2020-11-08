<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CanHelpWith;
use App\Models\NeedHelpWith;
use App\Models\FriendConnection;
use App\Models\User;
use App\Models\UserProfile;
use JWTAuth;
use Config;
use Helper;

class FriendSuggestion_Controller extends Controller
{
    public $response;
    public function __construct()
    {
        $this->response = [
            'response' => 1,
            'success' => 0,
            'message' => 'Invalid Request',
        ];

        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function friendSuggestion(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;
        $data = [];
        $ids_data = [];
        if(isset($user) && !empty($user)){
            if(1==2 && isset($request->category_ids) && !empty($request->category_ids)){
            //     $category_ids = explode(",", $request->category_ids);
            //     if(count($category_ids) != 0){
            //         $user_ids = CanHelpWith::whereIn('category_id', $category_ids)->groupBy('user_id')->pluck('user_id');
            //         if(count($user_ids) != 0){
            //             $users =  User::whereIn('id', $user_ids)->with('profile')->get();
            //             foreach($users as $key => $user){
            //                $push_data = [
            //                    'name' => $user->first_name." ".$user->last_name,
            //                    'profile_image' => asset('/public/storage/profile_images/'.$user->profile->profile_image),
            //                    'state' => null,
            //                    'city' => null,
            //                    'user_id' => $user->id
            //                ] ;
            //                array_push($data , $push_data);
            //             }
            //             $this->response['response'] = 1;
            //             $this->response['success'] = 1;
            //             $this->response['message'] = "Matched found";
            //             $this->response['suggestions'] = count($data);
            //             $this->response['data'] = $data ;
            //         }else{  
            //             $this->response['message'] = "0 user match";
            //         }
            //     }
            }else{
                $category_ids = NeedHelpWith::where('user_id', $user->id)->pluck('category_id');
                //dd($category_ids);
                if(count($category_ids)){
                    $friends_data = FriendConnection::where(function($q)use($user){
                        $q->where('requester_id', $user->id);
                        $q->Orwhere('requestee_id',$user->id);
                    })->get();
                    if( $friends_data->count() !=0 ){
                        foreach ($friends_data as $key => $friend) {
                           $ids_data[$friend->requester_id] = $friend->id;
                           $ids_data[$friend->requestee_id] = $friend->id;
                        }  
                    }else{
                        $ids_data[$user->id] = $user->id;
                    }
                                        
                    $user_ids = CanHelpWith::whereIn('category_id', $category_ids)->whereNotIn('user_id',  array_keys($ids_data))->groupBy('user_id')->pluck('user_id')->toArray();
                    
                    if(count($user_ids) != 0){
                        
                        $users =  User::whereIn('id', $user_ids)->with('profile')->get();
                        foreach($users as $key => $user){
                            $push_data = [
                               'name' => $user->first_name." ".$user->last_name,
                               'profile_image' => asset('/public/storage/profile_images/'.$user->profile->profile_image),
                               'state' => null,
                               'city' => null,
                               'user_id' => $user->id,
                               'about_info' => $user->profile->about_info
                            ] ;
                           array_push($data , $push_data);
                        }
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Matched found";
                        $this->response['suggestions'] = count($data);
                        $this->response['data'] = $data ;
                    }else{  
                        $this->response['message'] = "0 user match";
                    }
                }else{
                    $this->response['message'] = "No category found , please save some category to get suggestion";
                }
            }
        }else{
            $this->response['message'] = "User not found";
        } 
        return response()->json($this->response);
    }

    public function viewProfile(Request $request){
        if(isset($request->user_id) && !empty($request->user_id)){
            $user = User::where('id', $request->user_id)->first();
            $canHelpWith = CanHelpWith::where('user_id',$user->id)->pluck('category_id')->toArray();
            $needHelpWith = NeedHelpWith::where('user_id',$user->id)->pluck('category_id')->toArray(); 
            if(isset($user) && !empty($user)){
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
                $this->response['data']['need_help_with'] = implode(",", $canHelpWith);
                $this->response['data']['can_help_with'] = implode(",", $needHelpWith);
            }else{
                $this->response['message'] = "User not found";
            }
        }else{
            $this->response['message'] = "Data missing ";
        }
        return response()->json($this->response);
    }

    public function sendFriendRequest(Request $request){
        if(isset($request->user_id) && !empty($request->user_id)){
            Config::set('jwt.user', App\Models\User::class);
            $requester = JWTAuth::toUser(JWTAuth::getToken()); //whome request something
            $requestee = User::where('id', $request->user_id)->first(); // whome to request
            if((isset($requester) && !empty($requester)) && (isset($requestee) && !empty($requestee))) {
                if($requester->id == $requestee->id){
                    $this->response['message'] = "You can not send request to yourself";
                }else{
                     $check_request = FriendConnection::where('requester_id',$requester->id)->where('requestee_id',$requestee->id)->first();
                    if(isset($check_request) && !empty($check_request)){
                        $this->response['message'] = "You already send the friend request,please wait for their response";
                    }else{
                        $sendConnection = FriendConnection::create(['requester_id' => $requester->id ,'requestee_id'=> $requestee->id , 'status' => 0]);
                        if( $sendConnection){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message'] = "Friend request send";
                        }else{
                            $this->response['message'] = "Something went wrong";
                        }
                    }
                }
            }else{
                $this->response['message'] = "User not found";
            }
        }else{
            $this->response['message'] = "Data missing ";
        }
        return response()->json($this->response);
    }

    public function getFriendList(Request $request){
        $data = [];
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken()); 
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;
        $ids_data = [];
        if(isset($user) && !empty($user)){
            $friends_data = FriendConnection::where('status',1)->where(function($q)use($user){
                $q->where('requester_id', $user->id);
                $q->Orwhere('requestee_id',$user->id);
            })->get();
            $check_data = [];
            if(isset($friends_data) && !empty($friends_data)){
                foreach ($friends_data as $key => $friend) {
                   $ids_data[$friend->requester_id] = $friend->id;
                   $ids_data[$friend->requestee_id] = $friend->id;
                }
                unset($ids_data[$user->id]);
                //dd($ids_data);
                $users = User::whereIn('id',array_keys($ids_data))->with('profile');
                if(isset($users)){
                    $current_friend_list = $users->offset($offset)->limit($total_records_per_page)->get();
                    foreach($current_friend_list as $key => $friend){
                        $connection_id = "";
                        if(array_key_exists($friend->id,$ids_data)){
                            $connection_id = $ids_data[$friend->id];
                        }
                        $image = $friend->profile->profile_image;
                        array_push($data,[
                            'name' => $friend->first_name." ".$friend->last_name,
                            'user_id' => $friend->id,
                            'profile_image' => asset('/public/storage/profile_images/'.$image),
                            'about_info' => $friend->profile->about_info,
                            'connection_id' =>  $connection_id,
                        ]);
                    }
                    $global_count = $users->count();
                    if(count($data)){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['total_count'] = $global_count;
                        $this->response['data'] = $data;
                        $this->response['message'] = "Friend list found";
                    }else{
                        $this->response['message'] = "No data found ";
                    }
                }else{
                    $this->response['message'] = "No friend found";
                }
            }else{
                $this->response['message'] = "No data found"; 
            }
            
        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    public function getPendingFriendList(Request $request){
        $data = [];
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken()); 
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;
        if(isset($user) && !empty($user)){
            $requestee = FriendConnection::where('requester_id', $user->id)->where('status',0)->with('requestee');
            if(isset($requestee)){
                $current_friend_list = $requestee->offset($offset)->limit($total_records_per_page)->get();
                foreach($current_friend_list as $key => $friend){
                    $image = $friend->requestee->profile->profile_image;
                    array_push($data,[
                        'name' => $friend->requestee->first_name." ".$friend->requestee->last_name,
                        'user_id' => $friend->requestee->id,
                        'profile_image' => asset('/public/storage/profile_images/'.$image),
                        'connection_id' => $friend->id,
                        'about_info' => $friend->requestee->profile->about_info
                    ]);
                }
                $global_count = $requestee->count();
                if(count($data)){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['total_count'] = $global_count;
                    $this->response['data'] = $data;
                    $this->response['message'] = "Fetched successfully";
                }else{
                    $this->response['message'] = "No data found ";
                }
            }else{
                $this->response['message'] = "No friend found";
            }
        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    public function acceptFriendRequest(Request $request){
        if(isset($request->connection_id) && strlen($request->connection_id)!=0){
            $acceptFriend = FriendConnection::where('id', $request->connection_id)->first();
            if(isset($acceptFriend) && !empty($acceptFriend)){
                $first_user = UserProfile::where('user_id', $acceptFriend->requester_id)->first();
                $second_user = UserProfile::where('user_id', $acceptFriend->requestee_id)->first();
                if($acceptFriend->status == 0 || $acceptFriend->status == 2 || $acceptFriend->status == 3 || $acceptFriend->status == 5){
                    $first_user->friends_count = $first_user->friends_count+1 ;
                    $second_user->friends_count = $second_user->friends_count+1 ;
                    $first_user->save();
                    $second_user->save();
                    $acceptFriend->status = 1;
                    if($acceptFriend->save()){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Request accepted ";
                    }else{
                        $this->response['message'] = "Something went wrong";    
                    }
                }else{
                    $this->response['message'] = "Friend request is already accepted or blocked ";
                }
                
            }else{
                $this->response['message'] = "No data found";    
            }
        }else{
            $this->response['message'] = "Data missing";
        }
        return response()->json($this->response);
    
    }
    public function rejectFriendRequest(Request $request){
        if(isset($request->connection_id) && strlen($request->connection_id)!=0){
            $acceptFriend = FriendConnection::where('id', $request->connection_id)->first();
            if(isset($acceptFriend) && !empty($acceptFriend)){
                if($acceptFriend->status == 0){
                    $acceptFriend->status = 2;
                    if($acceptFriend->save()){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Request rejected ";
                    }else{
                        $this->response['message'] = "Something went wrong";    
                    }
                }else{
                     $this->response['message'] = "Your request ";   
                }
            }else{
                $this->response['message'] = "No data found";    
            }
        }else{
            $this->response['message'] = "Data missing";
        }
        return response()->json($this->response);
    
    }

    public function searchFriends(Request $request){
        $data =[];
        if(isset($request->email) && strlen($request->email)!=0){
            $user = User::where('email', $request->email)->with('profile')->first();
            if(isset($user) && !empty($user)){
                array_push($data , [
                    'name' => $user->first_name." ".$user->last_name,
                    'profile_image' => asset('/public/storage/profile_images/'.$user->profile->profile_image),
                    'state' => null,
                    'city' => null,
                    'user_id' => $user->id,
                    'about_info' => $user->profile->about_info
                ]);
                if(count($data) != 0){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['data'] = $data;
                    $this->response['message'] = "Match found";
                }else{
                    $this->response['message'] = "Something went wrong";    
                }
            }else{
                $this->response['message'] = "No data found";    
            }
        }else{
            $this->response['message'] = "Data missing";
        }
        return response()->json($this->response);
    }

    public function receviedFriendRequestList(Request $request){
        $data = [];
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken()); 
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;
        if(isset($user) && !empty($user)){
            $requester = FriendConnection::where('requestee_id', $user->id)->where('status',0)->with('requester');
            if(isset($requester)){
                $current_received_friend_list = $requester->offset($offset)->limit($total_records_per_page)->get();
                foreach($current_received_friend_list as $key => $friend){
                    $image = $friend->requester->profile->profile_image;
                    array_push($data,[
                        'name' => $friend->requester->first_name." ".$friend->requester->last_name,
                        'user_id' => $friend->requester->id,
                        'profile_image' => asset('/public/storage/profile_images/'.$image),
                        'connetion_id' => $friend->id,
                        'about_info' => $friend->requester->profile->about_info,
                        'connection_id' => $friend->id
                    ]);
                }
                $global_count = $requester->count();
                if(count($data)){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['total_count'] = $global_count;
                    $this->response['data'] = $data;
                    $this->response['message'] = "Fetched successfully";
                }else{
                    $this->response['message'] = "No data found ";
                }
            }else{
                $this->response['message'] = "No friend found";
            }
        }else{
            $this->response['message'] = "User not found";
        }
        return response()->json($this->response);
    }

    public function cancelFriendRequest(Request $request){
        if(isset($request->connection_id) && strlen($request->connection_id)!=0){
            $cancelRequest = FriendConnection::where('id', $request->connection_id)->first();
            if(isset($cancelRequest) && !empty($cancelRequest)){
                if($cancelRequest->status == 0){
                    $cancelRequest->status = 5;
                    if($cancelRequest->save()){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "cancel Request ";
                    }else{
                        $this->response['message'] = "Something went wrong";    
                    }
                }else{
                    $this->response['message'] = "You can't cancel it either friend request is accepted or rejected or unfirend state";    
                }
            }else{
                $this->response['message'] = "No data found";    
            }
        }else{
            $this->response['message'] = "Data missing";
        }
        return response()->json($this->response);
    }

    public function unfriendFriendRequest(Request $request){
        if(isset($request->connection_id) && strlen($request->connection_id)!=0){
            $unfriedRequest = FriendConnection::where('id', $request->connection_id)->first();
            if(isset($unfriedRequest) && !empty($unfriedRequest)){
                if($unfriedRequest->status == 1){
                    $first_user = UserProfile::where('user_id', $unfriedRequest->requester_id)->first();
                    $second_user = UserProfile::where('user_id', $unfriedRequest->requestee_id)->first();
                    $first_user->friends_count = ($first_user->friends_count != 0) ? $first_user->friends_count-1 : 0 ;
                    $second_user->friends_count = ($second_user->friends_count != 0) ? $second_user->friends_count -1 : 0 ;
                    $first_user->save();
                    $second_user->save();
                    $unfriedRequest->status = 3;
                    if($unfriedRequest->save()){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Unfriend !!! ";
                    }else{
                        $this->response['message'] = "Something went wrong";    
                    }
                }else{
                     $this->response['message'] = "In case of unfirend, you have to accept it first";
                }
            }else{
                $this->response['message'] = "No data found";    
            }
        }else{
            $this->response['message'] = "Data missing";
        }
        return response()->json($this->response);
    }

}