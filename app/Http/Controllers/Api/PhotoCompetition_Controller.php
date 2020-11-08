<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CompetitionPhoto;
use App\Models\PhotoVote;
use App\Models\State;
use App\Models\Country;
use App\Models\User;
use JWTAuth;
use Config;
use Helper;
use DB;
Use Exception;

class PhotoCompetition_Controller extends Controller
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

    /**
    *@ request : post 
    *@ return : insert photos for competition
    */

    public function submitPhoto(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $current_comp_id = date('mY');
        $state = "";
        $state_id = 0 ;
        $country = "";
        $country_id = 0;
        
        if(isset($user) && !empty($user)){

            if(isset($request->comp_id, $request->state_id, $request->country_id)){
                  
                if($request->comp_id === $current_comp_id){

                    //$check_state = State::where('id', $request->state_id)->first();                    
                    $check_state =  DB::table('countries')->select('*')->where('id', $request->state_id)->first();                    
                    if($check_state !== null){
                        $state = $check_state->name;
                        $state_id = $check_state->id;
                    }else{
                        $this->response['message'] = " State not found"; 
                    } 

                    //$check_country = Country::where('id', $request->country_id)->first();
                    $check_country =  DB::table('states')->select('*')->where('id', $request->state_id)->first();   
                    //dd($request->all(),$check_country, $check_state);
                    if($check_country !== null){
                        $country = $check_country->name;
                        $country_id = $check_country->id;
                    }else{
                       $this->response['message'] = " Country not found"; 
                    }

        			$check_photo = CompetitionPhoto::where('user_id', $user->id)->where('comp_id', $request->comp_id)->first();
        			if($check_photo === null){
                        if( $state_id && $country_id){
                            if($file = $request->file('photo')){
                                 $path = storage_path('app/public/photo_competition/');
                                if (!is_dir($path)) {
                                    mkdir($path, 0777, true);
                                }
                                $name =  $user->id.'_'.$request->comp_id.'.'.$file->getClientOriginalExtension();
                                $file->move(storage_path('app/public/photo_competition/'),$name);

                                $comp = CompetitionPhoto::create(['user_id' => $user->id, 'comp_id' => $current_comp_id , 'photo_path'=> $name, 'state' => $state , 'state_id' => $state_id , 'country' => $country , 'country_id' => $country_id]);
                                if($comp->id){
                                    $this->response['response'] = 1;
                                    $this->response['success'] = 1;
                                    $this->response['message'] = "Upload successful";
                                }else{
                                    $this->response['message'] = "Something went wrong";
                                } 
                            }else{
                                $this->response['message'] = " Please attached files ";  
                            }
                        }
	        		}else{
	        			$this->response['message'] = " You can't upload again for this month";	
	        		}
	        	}else{
	        		$this->response['message'] = "This competition yet not coming or over ,you can't upload photo for it ";
	        	}
	        }else{
	        	$this->response['message'] = "data missing !!!";
	        }
        }else{
        	$this->response['message'] = "Invalid user";	
        }
        	
        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : insert photos for competition
    */

    public function getPhotos(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page; 
        $current_comp_id = date('mY'); 
        $data = [];

        if(isset($user) && !empty($user)){
        	if(isset($request->state_id, $request->country_id)){
        		$photos = CompetitionPhoto::where('comp_id', $current_comp_id)->where('state_id', $request->state_id)->where('country_id', $request->country_id);
        		if($photos->count() != 0){
        			$photos = $photos->offset($offset)->limit($total_records_per_page)->get();
        			foreach ($photos as $key => $photo) {
        				array_push($data, [
        					'id' => $photo->id,
        					'photo_path' => asset('/public/storage/photo_competition/'.$photo->photo_path),
        					'votes_count' => $photo->votes_count,
        					'is_winner' => $photo->is_winner
        				]);
        			}
        			if(count($data)){
        				$this->response['response'] = 1;
	                	$this->response['success'] = 1;
	                	$this->response['message'] = "Data found";
	                	$this->response['data'] = $data;
        			}else{
        				$this->response['message'] = "No data found";
        			}
        		}else{
        			$this->response['message'] = "No data found";
        		}
	        }else{
	        	$this->response['message'] = "data missing !!!";
	        }
        }else{
        	$this->response['message'] = "Invalid user";	
        }
        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return :add vote
    */

    public function addVote(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

    	if(isset($user) && !empty($user)){
    		if(isset($request->photo_id)){
    			try {
                    $check_photo = CompetitionPhoto::where('id', $request->photo_id)->first();
                    if($check_photo !== null){
                        $photo = PhotoVote::create(['user_id'=> $user->id, 'photo_id' => $check_photo->id]);
                        CompetitionPhoto::where('id', $request->photo_id)->update(['votes_count'=> DB::raw('votes_count+1')]);
                        if($photo->id){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message'] = "Vote added";
                        }else{
                             $this->response['message'] = "Something went wrong";
                        }
                    }else{
                        $this->response['message'] = "No photo found";
                    }
    			} catch (Exception $e) {
    				if ($e->getCode() == 23000) {
				       $this->response['message'] = "You already vote for this photo";
				   	}
    			}
    		}else{
    			$this->response['message'] = "data missing !!!";		
    		}
    	}else{
        	$this->response['message'] = "Invalid user";	
        }
    	return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : get competition result 
    */

    public function getCompetitionResult(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $state_data = [];
        $country_data = [];
        if(isset($user) && !empty($user)){
        	if(isset($request->comp_id, $request->state_id, $request->country_id)){
        		$state_object = CompetitionPhoto::where('comp_id', $request->comp_id)->where('state_id', $request->state_id)->where('country_id', $request->country_id)->orderBy('votes_count', 'DESC')->take(3)->get();
        		$country_object = CompetitionPhoto::where('comp_id', $request->comp_id)->where('country_id', $request->country_id)->orderBy('votes_count', 'DESC')->take(3)->get();
                foreach ($state_object as $key => $state) {
                    $image = $state->user->profile->profile_image; 
                    array_push($state_data, [
                        'photo_id' => $state->id ,
                        'votes_count' => $state->votes_count ,
                        'is_winner' => $state->is_winner ,
                        'photo_path' => asset('/public/storage/photo_competition/'.$state->photo_path) ,
                        'user_id' => $state->user_id ,
                        'user_name' => $state->user->first_name." ".$state->user->last_name,
                        'user_email' => $state->user->email,
                        'user_profile_image'=> asset('/public/storage/profile_images/'.$image),
                        'created_at' => date("Y-m-d H:i:s" , strtotime($state->created_at)),
                    ]);
                    unset($image);
                }
                foreach ($country_object as $key => $country) {
                    $image = $country->user->profile->profile_image; 
                    array_push($country_data, [
                        'photo_id' => $country->id ,
                        'votes_count' => $country->votes_count ,
                        'is_winner' => $country->is_winner ,
                        'photo_path' => asset('/public/storage/photo_competition/'.$country->photo_path) ,
                        'user_id' => $country->user_id ,
                        'user_name' => $country->user->first_name." ".$country->user->last_name,
                        'user_email' => $country->user->email,
                        'user_profile_image'=> asset('/public/storage/profile_images/'.$image),
                        'created_at' => date("Y-m-d H:i:s" , strtotime($country->created_at)),
                    ]);
                    unset($image);
                }
                $current_date = date('Y-m-d');
                $lastday = date('Y-m-t',strtotime($current_date));
                //if(strtotime($current_date) == strtotime( $lastday)){
                    $this->response['response'] = 1;
                    $this->response['success'] = 1;
                    $this->response['message'] = "Fetched successfully";
                    $this->response['state_data'] = $state_data;
                    $this->response['country_data'] = $country_data;
                // }else{
                //     $this->response['message'] = "Awaited";
                // }
        	}else{
        		$this->response['message'] = "data missing !!!";		
        	}
        }else{
        	$this->response['message'] = "Invalid user";	
        }
    	return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : get competition result 
    */

    public function getUserPreviousEntries(Request $request){
    	$data = [] ;

        if(isset($request->user_id) && !empty($request->user_id)){
        	$user = User::where('id' , $request->user_id)->first();
        	if($user !== null){
        		$comp_data = CompetitionPhoto::where('user_id', $user->id)->orderBy('votes_count', 'DESC')->take(6)->get();
        		if($comp_data->count()){
        			foreach ($comp_data as $key => $comp) {
        				array_push($data, [
        					'photo_id' => $comp->id,
        					'photo_path' => asset('/public/storage/photo_competition/'.$comp->photo_path),
        					'votes_count' => $comp->votes_count,
        					'is_winner' => $comp->is_winner,
                            'created_at' => date("Y-m-d H:i:s" , strtotime($comp->created_at)),
        				]);
        			}
                    if(count($data)){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Data found";
                        $this->response['data'] = $data;
                    }else{
                        $this->response['message'] = "No data found";
                    }
        		}else{
                     $this->response['message'] = "No data found";
                }
        	}else{
        		$this->response['message'] = "No user found";
        	}
        }else{
        	$this->response['message'] = "Data missing";	
        }
    	return response()->json($this->response);
    }

}
