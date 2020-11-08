<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Models\Feed;
use App\Models\User;
use Auth;
use Helper;
use Config;
use DB;
use View;

class Feed_Controller extends Controller
{
    protected $search_data;

    public function __construct()
    {
        $this->middleware('admin_auth');
    }

    public function index(Request $request) {

		$feed_model = new Feed;
        $limit = Config::get('custom_setting.PER_PAGE_LIMIT');
        $global_count = $feed_model->count();
        $search_mode = false;
        $sr_dt = [];

        if($request->has('sr_dt')&&strlen($request->sr_dt)>0){
	        $sr_dt=Helper::convertSearchDataDecode($request->sr_dt);  
	        $feed_model=$this->searchfeeds($feed_model,$sr_dt);
	        $global_count=$feed_model->count();
	        $search_mode=true;
        }

		if($request->has('feed_cp')){
		    $currentPage=$request->feed_cp;
		}    

        if (isset($currentPage)) {
            if ($currentPage > ceil($global_count / $limit)) {
                $currentPage = ceil($global_count / $limit);
                $request->city_cp=$currentPage;
            }

            Paginator::currentPageResolver(function() use ($currentPage) {
                return $currentPage;
            });
        }

        $feeds = $feed_model->orderBy('id', 'desc')->paginate($limit, ['*'], 'feed_cp');    
        $total_count = $feeds->count();
        $rec_on_page = count($feeds);
        $page = ($request->has('feed_cp') && $request->feed_cp>1) ? $request->feed_cp : 1;
        $start = ($page>1) ? $limit*($page-1)+1 : (($rec_on_page>0)?1:0);
        $end = ($start<=1) ? $rec_on_page : $start+$rec_on_page-1;
        $pager_data = [ 'global_count' => $global_count, 'start' => $start, 'end' => $end ];

		if ($request->ajax()) {
			return view('admin.feed.feed_grid')->with(['feeds'=>$feeds])->with('search_mode',$search_mode)->with('sr_dt',$sr_dt)->with('pager_data',$pager_data)->render();
		}

		return view('admin.feed.list_feed')->with(['feeds'=>$feeds])->with('search_mode',$search_mode)->with('sr_dt',$sr_dt)->with('pager_data',$pager_data);
	}

	protected function searchfeeds($feed_model,$search_data){
        $model = $feed_model;

        if(isset($search_data['id'])&& strlen($search_data['id'])){
            $model=$model->where('id','=',$search_data['id']);
        }

        if(isset($search_data['email'])&& strlen($search_data['email'])){

        	$term=$search_data['email'];
            $model = $model->whereHas('user',function($q) use($term){
            	$q->where('email','like','%'.$term.'%');	
            });
        }

        return $model;
    }

    public function showFeedInfo(Request $request){

		$response="Invalid Feed!";
		if(isset($request->feed_id) && is_numeric($request->feed_id) && $request->feed_id>0){
			$feed=Feed::find($request->feed_id);
			$view = View::make('admin.feed.feed_details', ['feed' => $feed]);
			$response = $view->render();
		}
		echo ($response);
    }

    public function showUserInfo(Request $request){

		$response="Invalid User!";
		if(isset($request->user_id) && is_numeric($request->user_id) && $request->user_id>0){
			$user = User::find($request->user_id);
			$view = View::make('admin.feed.user_details', ['user' => $user]);
			$response = $view->render();
		}
		echo ($response);
    }

    public function approve(Request $request){
    	$response['response'] = 1;
	    $response['success']  = 0;
	    $response['message']  = "Data Missing!";
	    $feed = Feed::find($request->id);
	    if($feed){
	       $feed->status=1;
	       $feed->save();
	       $response['success']  = 1;
	       $response['message']  = "Approve successfully";	   
	       return response()->json($response);	    
	    }
	    else{
	         return redirect()->back()->withError("Invalid data!");
	    }

	}

	public function disapprove(Request $request){
    	$response['response'] = 1;
	    $response['success']  = 0;
	    $response['message']  = "Data Missing!";
	    $feed = Feed::find($request->id);
	    if($feed){
	       $feed->status=2;
	       $feed->save();
	       $response['success']  = 2;
	       $response['message']  = "Disapprove";	   
	       return response()->json($response);	    
	    }
	    else{
	         return redirect()->back()->withError("Invalid data!");
	    }

	}

}
