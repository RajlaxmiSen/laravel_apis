<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReportedIssue;
use App\Models\CompetitionPhoto;
use App\Models\Feed;
use JWTAuth;
use Config;
use Helper;
use DB;

class Reporting_Controller extends Controller
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

    public function reportingIssue(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
    	$data = [] ;

        if(isset($user) && !empty($user)){
        	if(isset($request->feed_id, $request->photo_id)){
        		$check_feed = null;
        		$check_photo = null ;
                $feed_report =null;
                $photo_report = null;
        		if($request->feed_id != 0){
        			$check_feed = Feed::where('id', $request->feed_id)->first();
        		}
        		if($request->photo_id != 0){
        			$check_photo = CompetitionPhoto::where('id', $request->photo_id)->first();
        		}
        		if($check_feed != null){
        			$feed_report = ReportedIssue::create(['user_id' => $user->id , 'feed_id' => $check_feed->id]);
        		}
        		if($check_photo != null){
        			$photo_report = ReportedIssue::create(['user_id' => $user->id , 'photo_id' => $check_photo->id]);
        		}

        		if($photo_report!=null || $feed_report!=null){
        			$this->response['response'] = 1;
					$this->response['success'] = 1;
					$this->response['message'] = "success";
        		}else{
        			$this->response['message'] = "Either feed or photo not found";	
        		}
        	}else{
        		$this->response['message'] = "Data missing";	
        	}
        }else{
        	$this->response['message'] = "User not found";	
        }
    	return response()->json($this->response);
    }
}
