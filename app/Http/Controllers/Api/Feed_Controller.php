<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Models\FeedComment;
use App\Models\FeedImage;
use App\Models\FeedVideo;
use App\Models\FeedLike;
use App\Models\FeedValueAdded;
use App\Models\UserProfile;
use App\Models\User;
use App\Models\FriendConnection;
use JWTAuth;
use Config;
use Helper;

class Feed_Controller extends Controller
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

    public function addFeed(Request $request){

    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
    	$images_url = [] ;
    	if(isset($user) && !empty($user)){

    		$feed_text = isset($request->feed_text) ? $request->feed_text : null ;
    		$feed = Feed::create(['user_id' => $user->id , 'feed_text' => $feed_text]);

            $user_profile = UserProfile::where('user_id', $user->id)->first();
            $user_profile->posts_count = $user_profile->posts_count+1;

	    	if($request->hasFile('feed_images')){

	    		$path = storage_path('app/public/feeds/'.$feed->id.'/images');
				if (!is_dir($path)) {
				    mkdir($path, 0777, true);
				}

			    $images = array();
			    $i = 0;
			    if($files = $request->file('feed_images')){
			        foreach($files as $file){
			            $name =  time().$i++.'.'.$file->getClientOriginalExtension();
			            $file->move(storage_path('app/public/feeds/'.$feed->id.'/images'),$name);
			            $images[]= $feed->id.'/images/'.$name;
			        }
			    }

			   	$feed_images = FeedImage::create(['feed_id' => $feed->id, 'image_path' => implode("|",$images)]);


		    	foreach ($images as $key => $image) {
		    		$images_url[] = asset('/storage/feeds/'.$image);
		    	}
	    	}

	    	if(isset($request->feed_video)){

	    		$path = storage_path('app/public/feeds/'.$feed->id.'/videos');
				if (!is_dir($path)) {
				    mkdir($path, 0777, true);
				}

			    $videos = "";
			    if($file= $request->file('feed_video')){
			    	$name =  time().'.'.$file->getClientOriginalExtension();
			        $file->move(storage_path('app/public/feeds/'.$feed->id.'/videos'),$name);
			        $videos= $feed->id.'/videos/'.$name;
			    }

			   	$feed_videos = FeedVideo::create(['feed_id' => $feed->id, 'video_path' => $videos]);
	    	}

	        if(isset($feed)){
                $user_profile->save();
	            $this->response['response'] = 1;
	            $this->response['success'] = 1;
	            $this->response['feed_id'] = $feed->id;
	            $this->response['feed_text'] = $feed_text;
	            $this->response['feed_images'] = $images_url;
	            $this->response['feed_video'] = isset($videos) ? asset('/storage/feeds/'.$videos) : "" ;
	            $this->response['message'] = "Feed Added ";
	        }else{
	        	$this->response['message'] = "Something went wrong" ;
	        }
    	}else{
    		$this->response['message'] = "User not found ";
    	}
        
        return response()->json($this->response);
    }

    public function removeFeed(Request $request){
    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
        	if(isset($request->feed_id)){
        		$feed = Feed::where('id',$request->feed_id)->where('user_id', $user->id)->with('images','video','like','comments','valueAdd')->first();
        		if(isset($feed) && !empty($feed)){
        			$images_path = [] ;
        			if(isset($feed->image->image_path)){
        				$images_path = explode("|", $feed->image->image_path);
        			}

				   	$video_path = isset($feed->video->video_path) ? $feed->video->video_path : "" ;
				   	//dd($images_path ,$video_path);
				   	if(count($images_path) != 0){
				   		if(file_exists( asset('/storage/feeds/'.$images_path[0]))){
				      		shell_exec( "rm -r -f /public/storage/feeds/".$feed->id."/images/*.*" );
				   		}
                        if(isset($feed->images)){
				   		   $feed->images->delete();
                        }
				   	}
				   	
				   	if(strlen($video_path) != 0){
				   		if(file_exists(asset('/storage/feeds/'.$video_path))){
					      	shell_exec( "rm -r -f /public/storage/feeds/".$feed->id."/videos/*.*" );
				   		}
                        if(isset($feed->video)){
					   	   $feed->video->delete();
                        }
				   	}
				   	if(isset($feed->like)){
                        $feed->like->delete();
                    }
                    if(isset($feed->comments)){
                        if($feed->comments->count() != 0){
                            foreach ($feed->comments as $key => $comment) {
                                $comment->delete();
                            }
                        }
                    }
                    if(isset($feed->valueAdd)){
                        $feed->valueAdd->delete();
                    }
				 	//$feed->image->delete();
				 	//$feed->video->delete();
                    $user_profile = UserProfile::where('user_id', $user->id)->first();
                    $user_profile->posts_count = ($user_profile->posts_count != 0) ? $user_profile->posts_count-1 : 0 ;
                    $user_profile->save();

        			if($feed->delete()){
        				$this->response['response'] = 1;
	            		$this->response['success'] = 1;
	            		$this->response['message'] = "Feed deleted !!! ";
        			}else{
        				$this->response['message'] = "Something went wrong";
        			}
        		}else{
        			$this->response['message'] = "No feed found";
        		}
        	}else{
        		$this->response['message'] = "Data missing" ;
        	}
        }else{
        	$this->response['message'] = "User not found ";
        }

    	return response()->json($this->response);
    }

    public function likeFeed(Request $request){

    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
        	if(isset($request->feed_id)){
        		$feed = Feed::where('id',$request->feed_id)->first();
        		if(isset($feed) && !empty($feed)){
        			$check_feed_like = FeedLike::where('feed_id', $feed->id)->where('user_id', $user->id)->first();
        			if(!isset($check_feed_like) && empty($check_feed_like)){
        				$likes = $feed->likes_count;
					   	$feed->likes_count = $likes+1;
					   	$feed_like = FeedLike::create(['feed_id' => $feed->id, 'user_id' => $user->id]);
	        			if($feed->save()){
	        				$this->response['response'] = 1;
		            		$this->response['success'] = 1;
		            		$this->response['message'] = "Liked";
		            		$this->response['liked_id'] = $feed_like->id;
		            		$this->response['feed_id'] = $feed->id;
	        			}else{
	        				$this->response['message'] = "Something went wrong";
	        			}
        			}else{
        				$this->response['message'] = "Already liked";
        			} 	
        		}else{
        			$this->response['message'] = "No feed found";
        		}
        	}else{
        		$this->response['message'] = "Data missing" ;
        	}
        }else{
        	$this->response['message'] = "User not found ";
        }

    	return response()->json($this->response);
    }

    public function unlikeFeed(Request $request){

    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
        	if(isset($request->feed_id)){
        		$feed = Feed::where('id',$request->feed_id)->first();
        		if(isset($feed) && !empty($feed)){
        			$check_feed_like = FeedLike::where('feed_id', $feed->id)->where('user_id', $user->id)->first();
        			if(isset($check_feed_like) && !empty($check_feed_like)){
        				$likes = $feed->likes_count;
					   	$feed->likes_count = $likes-1;
					   	$check_feed_like->delete();
	        			if($feed->save()){
	        				$this->response['response'] = 1;
		            		$this->response['success'] = 1;
		            		$this->response['message'] = "Unlike";
		            		//$this->response['liked_id'] = $feed_like->id;
		            		$this->response['feed_id'] = $feed->id;
	        			}else{
	        				$this->response['message'] = "Something went wrong";
	        			}
        			}else{
        				$this->response['message'] = "Like not found";
        			} 	
        		}else{
        			$this->response['message'] = "No feed found";
        		}
        	}else{
        		$this->response['message'] = "Data missing" ;
        	}
        }else{
        	$this->response['message'] = "User not found ";
        }

    	return response()->json($this->response);
    
    }

    public function addFeedComment(Request $request){

    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        //dd($user->userProfile);
        if(isset($user) && !empty($user)){
        	if(isset($request->feed_id, $request->comment_text)){
        		$feed = Feed::where('id',$request->feed_id)->with("user","userProfile")->first();
                //dd($feed);
        		if(isset($feed) && !empty($feed)){
					$comment = FeedComment::create(['feed_id' => $feed->id, 'user_id' => $user->id, 'comment' => $request->comment_text]);
	        		if($comment->id){
                        $image = $user->profile->profile_image; 
	        			$this->response['response'] = 1;
		            	$this->response['success'] = 1;
		            	$this->response['message'] = "Comment save";
                        $this->response['comment_id'] = $comment->id;
                        $this->response['comment_text'] = $comment->comment;
		            	$this->response['created_at'] = date("Y-m-d H:i:s" , strtotime($feed->created_at));
		            	$this->response['feed_id'] = $feed->id;
                        $this->response['user_id'] = $user->id;
                        $this->response['user_name'] = $user->first_name." ".$user->last_name;
                        $this->response['profile_image'] = asset('/public/storage/profile_images/'.$image);
                        unset($image);
	        		}else{
	        			$this->response['message'] = "Something went wrong";
	        		}	
        		}else{
        			$this->response['message'] = "No feed found";
        		}
        	}else{
        		$this->response['message'] = "Data missing" ;
        	}
        }else{
        	$this->response['message'] = "User not found ";
        }

    	return response()->json($this->response);
    }

    public function deleteFeedComment(Request $request){

    	Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
        	if(isset($request->comment_id)){
        		$comment = FeedComment::where('user_id', $user->id)->where('id', $request->comment_id);
        		if(isset($comment) && !empty($comment)){
	        		if($comment->delete()){
	        			$this->response['response'] = 1;
		            	$this->response['success'] = 1;
		            	$this->response['message'] = "Comment delete";
	        		}else{
	        			$this->response['message'] = "Something went wrong";
	        		}	
        		}else{
        			$this->response['message'] = "Comment not found";
        		}
        	}else{
        		$this->response['message'] = "Data missing" ;
        	}
        }else{
        	$this->response['message'] = "User not found ";
        }

    	return response()->json($this->response);
    }

    public function getFriendFeeds(Request $request){

        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;  
        $data = [];
        
        if(isset($request->user_id)){
            $user = User::where('id', $request->user_id)->first();
            if(isset($user) && !empty($user)){
                $feeds = Feed::where('user_id', $user->id)->orderBy('created_at', 'desc');
                if($feeds->count() != 0 ){
                    $current_page = $feeds->with('images','video','comments','like')->offset($offset)->limit($total_records_per_page)->get();
                    foreach($current_page as $key => $feed){
                        $images = [];
                        $shared_feed_user_id = "";
                        $shared_feed_user_name = "";
                        $shared_feed_user_about_info = "";
                        $shared_feed_user_profile_image = "";
                        $video = [] ;
                        if($feed->is_share == 1){
                            $shared_feed = Feed::where('id', $feed->shared_feed_id)->with('user','userProfile')->first();
                            if(isset($shared_feed) && !empty($shared_feed)){
                                $shared_feed_user_id = $shared_feed->user->id;
                                $shared_feed_user_name = $shared_feed->user->first_name." ".$shared_feed->user->last_name;
                                $shared_feed_user_name = $shared_feed->user->first_name." ".$shared_feed->user->last_name;
                                $shared_feed_user_about_info = $shared_feed->userProfile->about_info;
                                $shared_feed_user_profile_image = $shared_feed->userProfile->profile_image;
                            }

                            $shared_image = FeedImage::where('feed_id', $feed->shared_feed_id)->with('user')->first();

                            if(isset($shared_image) && !empty($shared_image)){
                                $images = isset($shared_images->images->image_path) ? explode("|", $shared_images->images->image_path) : [];
                            }

                            $shared_video = FeedVideo::where('feed_id', $feed->shared_feed_id)->with('user')->first();

                            if(isset($shared_video) && !empty($shared_video)){
                                $video = isset($shared_video->video->video_path) ? asset('/public/storage/feeds/'.$shared_video->video->video_path) : "" ;
                            }
                            
                        }else{
                            $images = isset($feed->images->image_path) ? explode("|", $feed->images->image_path) : [];
                            $video = isset($feed->video->video_path) ? asset('/public/storage/feeds/'.$feed->video->video_path) : "" ;
                        }
                        $images_url = [];
                        foreach ($images as $key => $image) {
                            $images_url[] = asset('/public/storage/feeds/'.$image);
                        }
                        
                        $image = $feed->userProfile->profile_image; 
                        array_push($data,[
                            'feed_id' => $feed->id,
                            'feed_text' => $feed->feed_text,
                            'feed_images' => $images_url,
                            'feed_video' => $video,
                            'likes_count' => $feed->likes_count,
                            'like_id' => isset($feed->like) ? $feed->like->id : 0,
                            'value_id' => isset($feed->valueAdd) ? $feed->valueAdd->id : 0,
                            'share_count' => $feed->share_count, 
                            'value_added' => (int)$feed->value_added,
                            'created_at' => date("Y-m-d H:i:s" , strtotime($feed->created_at)),
                            'user_id' => $feed->user->id,
                            'user_name' => $feed->user->first_name." ".$feed->user->last_name,
                            'about_info' => $feed->userProfile->about_info,
                            'profile_image' => asset('/public/storage/profile_images/'.$image),
                            'comments_count' => $feed->comments->count(),
                            'is_shared' => $feed->is_share,
                            'shared_feed_id' => $feed->shared_feed_id,
                            'shared_feed_user_id' => $shared_feed_user_id,
                            'shared_feed_user_name' => $shared_feed_user_name,
                            'shared_feed_user_about_info'=>$shared_feed_user_about_info,
                            'shared_feed_user_profile_image' => $shared_feed_user_profile_image
                        ]);
                        unset($image);
                    }
                    $global_count = $feeds->count();
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
                $this->response['message'] = "No feed found ";  
            }
            }else{
                $this->response['message'] = "User not found ";
            }
        }else{
            $this->response['message'] = "Data missing !!!";
        }
        return response()->json($this->response);
    }


    public function getFeedComments(Request $request){

        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;  
        $data = [];
        
        if(isset($request->feed_id)){
            if(isset($user) && !empty($user)){
                $feed = Feed::where('id', $request->feed_id)->first();
                //dd($feed);
                if(isset($feed) && !empty($feed) && $feed->count()){
                    $comments = FeedComment::where('feed_id', $request->feed_id)->orderBy('created_at', 'desc');
                    //dd($comments->get());
                    if($comments->count() != 0){
                        $current_page = $comments->with('user')->offset($offset)->limit($total_records_per_page)->get();
                        foreach($current_page as $key => $comment){
                            $image = $comment->userProfile->profile_image; 
                            array_push($data,[
                                'comment_id' => $comment->id,
                                'comment_text' => $comment->comment,
                                'user_name' => $comment->user->first_name." ".$comment->user->last_name,
                                'profile_image' => asset('/public/storage/profile_images/'.$image),
                                'user_id' => $comment->user->id,
                                'create_at' => date("Y-m-d H:i:s" , strtotime($comment->created_at))
                            ]);
                        }
                        $global_count = $comments->count();
                        if(count($data)){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['total_count'] = $global_count;
                            $this->response['data'] = $data;
                            $this->response['message'] = "Comments found";
                        }else{
                            $this->response['message'] = "No data found ";
                        }
                    }else{
                        $this->response['message'] = "No comments found";
                    }
                }else{
                    $this->response['message'] = "Feed not found";
                }
            }else{
                $this->response['message'] = "User not found ";
            }
        }else{
            $this->response['message'] = "Data missing ";
        }
        return response()->json($this->response);
    }

    /**
    *@ request : get 
    *@ return : return home feed for a user 
    */
    public function getHomeFeeds(Request $request){
        //dd($request->all());
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());
        //dd($request->page);
        $page = isset($request->page) ? $request->page : 1; 
        $total_records_per_page = 10;
        $offset = ($page-1) * $total_records_per_page;  
        $data = [];
        $ids_data = [];
        $is_video = isset($request->is_video) && ($request->is_video == "true")? true : false ;
        //dd($is_video); 
        if(isset($user) && !empty($user)){
            $friends_data = FriendConnection::where('status',1)->where(function($q)use($user){
                $q->where('requester_id', $user->id);
                $q->Orwhere('requestee_id',$user->id);
            })->get();

            if(isset($friends_data) && !empty($friends_data)){
                if($friends_data->count() != 0){
                   foreach ($friends_data as $key => $friend) {
                       $ids_data[$friend->requester_id] = $friend->id;
                       $ids_data[$friend->requestee_id] = $friend->id;
                    } 
                }else{
                    $ids_data[$user->id] = $user->id;
                }

                $feeds = Feed::whereIn('user_id', array_keys($ids_data))->orderBy('created_at', 'desc');

                if($feeds->count() != 0 ){
                    //dd($offset);
                    $current_page = $feeds->with('images','video','comments','like')->offset($offset)->limit($total_records_per_page)->get();
                    foreach($current_page as $key => $feed){
                        $images = [];
                        $shared_feed_user_id = "";
                        $shared_feed_user_name = "";
                        $shared_feed_user_about_info = "";
                        $shared_feed_user_profile_image = "";
                        $shared_time = "";
                        $video = "" ;
                        if($feed->is_share == 1){
                            $shared_feed = Feed::where('id', $feed->shared_feed_id)->with('user','userProfile')->first();
                            //dd($shared_feed);
                            if(isset($shared_feed) && !empty($shared_feed)){
                                $shared_feed_user_id = $shared_feed->user->id;
                                $shared_feed_user_name = $shared_feed->user->first_name." ".$shared_feed->user->last_name;
                                $shared_feed_user_name = $shared_feed->user->first_name." ".$shared_feed->user->last_name;
                                $shared_feed_user_about_info = $shared_feed->userProfile->about_info;
                                $shared_feed_user_profile_image = $shared_feed->userProfile->profile_image;
                                $shared_time =  date("Y-m-d H:i:s" , strtotime($shared_feed->created_at));
                            }

                            $shared_image = FeedImage::where('feed_id', $feed->shared_feed_id)->with('user')->first();

                            if(isset($shared_image) && !empty($shared_image)){
                                $images = isset($shared_images->images->image_path) ? explode("|", $shared_images->images->image_path) : [];
                            }

                            $shared_video = FeedVideo::where('feed_id', $feed->shared_feed_id)->with('user')->first();

                            if(isset($shared_video) && !empty($shared_video)){
                                $video = isset($shared_video->video->video_path) ? asset('/public/storage/feeds/'.$shared_video->video->video_path) : "" ;
                            }
                            
                        }else{
                            $images = isset($feed->images->image_path) ? explode("|", $feed->images->image_path) : [];
                            $video = isset($feed->video->video_path) ? asset('/public/storage/feeds/'.$feed->video->video_path) : "" ;
                        }
                        $images_url = [];
                        foreach ($images as $key => $image) {
                            $images_url[] = asset('/public/storage/feeds/'.$image);
                        }
                        $image = $feed->userProfile->profile_image; 
                        if(isset($is_video) &&  $is_video == true && strlen($video) > 0 ){
                            array_push($data,[
                                'feed_id' => $feed->id,
                                'feed_text' => $feed->feed_text,
                                'feed_images' => ($is_video == true) ? [] : $images_url,
                                'feed_video' => $video,
                                'likes_count' => $feed->likes_count,
                                'like_id' => isset($feed->like) ? $feed->like->id : 0,
                                'value_id' => isset($feed->valueAdd) ? $feed->valueAdd->id : 0,
                                'share_count' => $feed->share_count, 
                                'value_added' => (int)$feed->value_added,
                                'created_at' => date("Y-m-d H:i:s" , strtotime($feed->created_at)),
                                'user_id' => $feed->user->id,
                                'user_name' => $feed->user->first_name." ".$feed->user->last_name,
                                'about_info' => $feed->userProfile->about_info,
                                'profile_image' => asset('/public/storage/profile_images/'.$image),
                                'comments_count' => $feed->comments->count(),
                                'is_shared' => $feed->is_share,
                                'shared_feed_id' => $feed->shared_feed_id,
                                'shared_feed_user_id' => $shared_feed_user_id,
                                'shared_feed_user_name' => $shared_feed_user_name,
                                'shared_feed_user_about_info'=>$shared_feed_user_about_info,
                                'shared_feed_user_profile_image' => strlen($shared_feed_user_profile_image) !=0 ? asset('/public/storage/profile_images/'.$shared_feed_user_profile_image) : $shared_feed_user_profile_image,
                                'shared_created_at' => $shared_time,
                            ]);
                            unset($image);
                        }
                        if(isset($is_video) &&  $is_video == false){
                            array_push($data,[
                                'feed_id' => $feed->id,
                                'feed_text' => $feed->feed_text,
                                'feed_images' => $images_url,
                                'feed_video' => $video,
                                'likes_count' => $feed->likes_count,
                                'like_id' => isset($feed->like) ? $feed->like->id : 0,
                                'value_id' => isset($feed->valueAdd) ? $feed->valueAdd->id : 0,
                                'share_count' => $feed->share_count, 
                                'value_added' => (int)$feed->value_added,
                                'created_at' => date("Y-m-d H:i:s" , strtotime($feed->created_at)),
                                'user_id' => $feed->user->id,
                                'user_name' => $feed->user->first_name." ".$feed->user->last_name,
                                'about_info' => $feed->userProfile->about_info,
                                'profile_image' => asset('/public/storage/profile_images/'.$image),
                                'comments_count' => $feed->comments->count(),
                                'is_shared' => $feed->is_share,
                                'shared_feed_id' => $feed->shared_feed_id,
                                'shared_feed_user_id' => $shared_feed_user_id,
                                'shared_feed_user_name' => $shared_feed_user_name,
                                'shared_feed_user_about_info'=>$shared_feed_user_about_info,
                                'shared_feed_user_profile_image' => strlen($shared_feed_user_profile_image) !=0 ? asset('/public/storage/profile_images/'.$shared_feed_user_profile_image) : $shared_feed_user_profile_image,
                                'shared_created_at' => $shared_time,
                            ]);
                            unset($image);
                        }
                        
                    }
                    $global_count = $feeds->count();
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
                     $this->response['message'] = "No feed found ";  
                }
            }else{
                $this->response['message'] = "Do you have no friends , please make some friends :)";
            }
        }else{
            $this->response['message'] = "User not found ";
        }

        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : insert shared feed
    */
    public function feedShare(Request $request){
        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($request->feed_id) && !empty($request->feed_id)){
            $original_feed = Feed::where('id', $request->feed_id)->first();
            if(isset($original_feed) && isset($user)){
                $feed_text = isset($request->feed_text) ? $request->feed_text : null ;
                $share_count = $original_feed->share_count + 1;
                $shared_feed = Feed::where('user_id',$user->id)->where('shared_feed_id',$original_feed->id)->first();
                if(!isset($shared_feed) && empty($shared_feed)){
                    $feed = Feed::create(['user_id' => $user->id , 'feed_text' => $feed_text, 'share_count' =>$share_count,  'is_share' => 1 , 'shared_feed_id' => $original_feed->id]);
                    if($feed->id){
                        $this->response['response'] = 1;
                        $this->response['success'] = 1;
                        $this->response['message'] = "Feed shared";
                    }else{
                         $this->response['message'] = "Something went wrong " ; 
                    }
                }else{
                     $this->response['message'] = "You already share this feed" ;
                }
            }else{
                 $this->response['message'] = "Either feed or user is Invalid" ; 
            }

        }else{
            $this->response['message'] = "Data missing" ; 
        }
        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : insert shared feed
    */
    public function valueAdd(Request $request){

        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
            if(isset($request->feed_id)){
                $feed = Feed::where('id',$request->feed_id)->first();
                if(isset($feed) && !empty($feed)){
                    $check_feed_value = FeedValueAdded::where('feed_id', $feed->id)->where('user_id', $user->id)->first();
                    if(!isset($check_feed_value) && empty($check_feed_value)){
                        $likes = $feed->value_added;
                        $feed->value_added = $likes+1;
                        $feed_value = FeedValueAdded::create(['feed_id' => $feed->id, 'user_id' => $user->id]);
                        if($feed->save()){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message'] = "Value added ";
                            $this->response['value_id'] = $feed_value->id;
                            $this->response['feed_id'] = $feed->id;
                        }else{
                            $this->response['message'] = "Something went wrong";
                        }
                    }else{
                        $this->response['message'] = "Already value is added";
                    }   
                }else{
                    $this->response['message'] = "No feed found";
                }
            }else{
                $this->response['message'] = "Data missing" ;
            }
        }else{
            $this->response['message'] = "User not found ";
        }

        return response()->json($this->response);
    }

    /**
    *@ request : post 
    *@ return : remove value
    */

    public function valueRemove(Request $request){

        Config::set('jwt.user', App\Models\User::class);
        $user =JWTAuth::toUser(JWTAuth::getToken());

        if(isset($user) && !empty($user)){
            if(isset($request->feed_id)){
                $feed = Feed::where('id',$request->feed_id)->first();
                if(isset($feed) && !empty($feed)){
                    $check_feed_value = FeedValueAdded::where('feed_id', $feed->id)->where('user_id', $user->id)->first();
                    if(isset($check_feed_value) && !empty($check_feed_value)){
                        $value = $feed->value_added;
                        $feed->value_added = $value-1;
                        $check_feed_value->delete();
                        if($feed->save()){
                            $this->response['response'] = 1;
                            $this->response['success'] = 1;
                            $this->response['message'] = "Value remove";
                            //$this->response['liked_id'] = $feed_like->id;
                            $this->response['feed_id'] = $feed->id;
                        }else{
                            $this->response['message'] = "Something went wrong";
                        }
                    }else{
                        $this->response['message'] = "Value not found";
                    }   
                }else{
                    $this->response['message'] = "No feed found";
                }
            }else{
                $this->response['message'] = "Data missing" ;
            }
        }else{
            $this->response['message'] = "User not found ";
        }

        return response()->json($this->response);
    
    }

}
