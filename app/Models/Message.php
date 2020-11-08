<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    protected $dates = ['deleted_at'];
    protected $fillable = ['from_user','to_user','message', 'is_read'];

    public function fromUser(){
        return $this->belongsTo('App\Models\User','from_user');
    }
    public function toUser(){
        return $this->belongsTo('App\Models\User','to_user');
    }

    public function fromUserProfile(){
        return $this->belongsTo('App\Models\UserProfile','from_user');
    }
    public function toUserProfile(){
        return $this->belongsTo('App\Models\UserProfile','to_user');
    }
}
