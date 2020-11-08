<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use Auth;
use Helper;
use Config;
use DB;
use View;

class User_Controller extends Controller
{
    protected $search_data;

    public function __construct()
    {
        $this->middleware('admin_auth');
    }

    public function index(Request $request) {

		$model = new User;
        $limit = Config::get('custom_setting.PER_PAGE_LIMIT');
        $global_count = $model->count();
        $search_mode = false;
        $sr_dt = [];

        if($request->has('sr_dt')&&strlen($request->sr_dt)>0){
	        $sr_dt=Helper::convertSearchDataDecode($request->sr_dt);  
	        $model=$this->search($model,$sr_dt);
	        $global_count=$model->count();
	        $search_mode=true;
        }

		if($request->has('cp')){
		    $currentPage=$request->cp;
		}    

        if (isset($currentPage)) {
            if ($currentPage > ceil($global_count / $limit)) {
                $currentPage = ceil($global_count / $limit);
                $request->cp=$currentPage;
            }

            Paginator::currentPageResolver(function() use ($currentPage) {
                return $currentPage;
            });
        }

        $users = $model->orderBy('id', 'desc')->paginate($limit, ['*'], 'cp');    
        $total_count = $users->count();
        $rec_on_page = count($users);
        $page = ($request->has('cp') && $request->cp>1) ? $request->cp : 1;
        $start = ($page>1) ? $limit*($page-1)+1 : (($rec_on_page>0)?1:0);
        $end = ($start<=1) ? $rec_on_page : $start+$rec_on_page-1;
        $pager_data = [ 'global_count' => $global_count, 'start' => $start, 'end' => $end ];

		if ($request->ajax()) {
			return view('admin.user.user_grid')->with(['users'=>$users])->with('search_mode',$search_mode)->with('sr_dt',$sr_dt)->with('pager_data',$pager_data)->render();
		}

		return view('admin.user.list_user')->with(['users'=>$users])->with('search_mode',$search_mode)->with('sr_dt',$sr_dt)->with('pager_data',$pager_data);
	}

	protected function search($model,$search_data){

		$model = $model;

        if(isset($search_data['name'])&& strlen($search_data['name'])){
            $model=$model->where('first_name','like',"%".$search_data['name']."%");
        }
        //dd($model);
        if(isset($search_data['email'])&& strlen($search_data['email'])){
            $model=$model->where('email','=', $search_data['email']);
        }

        if(isset($search_data['mobile'])&& strlen($search_data['mobile'])){
            $model=$model->where('mobile','=',$search_data['mobile']);
        }

        // if(isset($search_data['email'])&& strlen($search_data['email'])){

        // 	$term=$search_data['email'];
        //     $model = $model->whereHas('user',function($q) use($term){
        //     	$q->where('name','like','%'.$term.'%');	
        //     });
        // }

        return $model;
    }

}
