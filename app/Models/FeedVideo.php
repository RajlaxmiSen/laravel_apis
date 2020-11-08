<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedVideo extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['feed_id', 'video_path'];

    public function feed() {
        return $this->belongsTo('App\Models\Feed', 'feed_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function userProfile() {
        return $this->belongsTo('App\Models\UserProfile','user_id');
    }
}
