<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FcmToken extends Model
{
	//use SoftDeletes;

    protected $table='fcm_tokens';
    protected $dates = ['deleted_at'];
    protected $fillable=['user_id','fcm_token','imei'];
}
