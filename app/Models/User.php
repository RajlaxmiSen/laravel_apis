<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name', 'dob','mobile','email', 'password','login_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function profile()
    {
        return $this->hasOne('App\Models\UserProfile','user_id');
    }

    public function canHelpWtih(){

      return $this->hasMany('App\Models\CanHelpWith','user_id');
    }

    public function needHelpWtih(){
      
      return $this->hasMany('App\Models\NeedHelpWith', 'user_id');
    }

    public function friends(){
      return $this->hasMany('App\Models\FriendConnection','requestee_id')->where('status',1);
    }

    public function requesterFriends(){
      return $this->hasMany('App\Models\FriendConnection','requester_id')->where('status',1);
    }

    public function feeds(){
      return $this->hasMany('App\Models\Feed','user_id');
    }
}
