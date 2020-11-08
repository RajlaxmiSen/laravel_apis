<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FriendConnection extends Model
{


    protected $dates = ['deleted_at'];
    protected $fillable = ['requester_id','requestee_id','status'];

    public function requester(){
        return $this->belongsTo('App\Models\User','requester_id');
    }
    public function requestee(){
        return $this->belongsTo('App\Models\User','requestee_id');
    }
    
}
