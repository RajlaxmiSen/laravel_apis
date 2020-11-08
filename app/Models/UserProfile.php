<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'user_id','mobile_visibility', 'email_visibility','profile_image','about_info', 'posts_count','friends_count', 'state', 'state_id', 'country', 'country_id'
    ];
    
    protected $dates = ['deleted_at'];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
