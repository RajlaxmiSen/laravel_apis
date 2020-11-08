<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\FriendConnection;
use JWTAuth;
use Config;
use Storage;
use DB;
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
    }

    /**
    *@ request : get 
    *@ return : Delete message 
    */

    public function deleteMessages(Request $request){
    	$t = time() - 86400 ;
    	$date = date("Y-m-d H:m:s",$t);
    	$message = Message::whereDate('created_at','<',$date)->delete();
    }
}
